<?php

namespace WithCandour\AardvarkSeo\Storage;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Statamic\Sites\Site as SiteObject;
use Statamic\Facades\File;
use Statamic\Facades\Site;
use Statamic\Facades\YAML;

class DatabaseStorage implements Storage
{
    const TABLE = 'aardvark_seo_storage';

    /**
     * Retrieve YAML data from storage
     *
     * @param string $handle
     * @param Site $site
     * @param bool $returnCollection
     *
     * @return array|Collection
     */
    public static function getYaml(string $handle, SiteObject $site, bool $returnCollection = false)
    {
        $record = self::query()
            ->where('handle', $handle)
            ->first();

        $data = YAML::parse($record?->yaml);

        $site_data = collect($data)->get($site->handle());

        if ($returnCollection) {
            return collect($site_data);
        }

        return collect($site_data)->toArray() ?: [];
    }

    /**
     * Retrieve YAML data from storage but back up using the default site
     *
     * @param string $handle
     * @param Site $site
     * @param bool $returnCollection
     *
     * @return array
     */
    public function getYamlWithBackup(string $handle, SiteObject $site, bool $returnCollection = false)
    {
        $storage = self::getYaml($handle, $site, true);

        if (Site::hasMultiple() && $site !== Site::default()) {
            $default_storage = self::getYaml($handle, Site::default(), true);
            $storage = $default_storage->merge($storage);
        }

        if ($returnCollection) {
            return $storage;
        }

        return $storage->toArray() ?: [];
    }

    /**
     * Put YAML data into storage
     *
     * @param string $handle
     * @param Site $site
     * @param array $data
     *
     * @return void
     */
    public static function putYaml(string $handle, SiteObject $site, array $data)
    {
        $record = self::query()
            ->where('handle', $site->handle())
            ->first();

        $existing = collect(YAML::parse($record?->yaml));

        $combined_data = $existing->merge([
            "{$site->handle()}" => $data,
        ])->toArray();

        DB::table(self::TABLE)->updateOrInsert([
            'handle' => $handle,
        ], [
            'yaml' => YAML::dump($combined_data),
        ]);
    }

    /**
     * @return Builder
     */
    protected static function query(): Builder
    {
        $connection = Config::get(
            'aardvark-seo.database_connection',
            Config::get('database.default')
        );

        return DB::connection($connection)->table(self::TABLE);
    }
}
