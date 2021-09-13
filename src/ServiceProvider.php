<?php
declare(strict_types=1);

namespace Rosamarsky\LaravelDoctrineOdm;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Types\Type;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Rosamarsky\LaravelDoctrineOdm\Types\CarbonDateType;
use MongoDB\Client;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        AnnotationRegistry::registerLoader('class_exists');

        $this->app->bind(DocumentManager::class, function () {
            $config = new Configuration();
            if (! $configs = $this->app->make('config')->get('doctrine-odm.manager')) {
                throw new \Exception('No config file provided for doctrine-odm.');
            }

            $config->setProxyDir($configs['proxy_dir']);
            $config->setProxyNamespace($configs['proxy_namespace']);
            $config->setHydratorDir($configs['hydrator_dir']);
            $config->setHydratorNamespace($configs['hydrator_namespace']);
            $config->setDefaultDB($configs['default_db']);
            $config->setMetadataDriverImpl(AnnotationDriver::create($configs['metadata_dir']));

            Type::registerType(CarbonDateType::CARBON, CarbonDateType::class);

            return DocumentManager::create($this->resolveMongoClient(), $config);
        });
    }

    protected function resolveMongoClient(): Client
    {
        if (! $config = $this->app->make('config')->get('doctrine-odm.database.mongodb')) {
            throw new \Exception('No config file provided for mongodb client.');
        }

        $client = new Client($config['dsn'], $config['auth'], $config['options']);
        $client->selectDatabase($config['auth']['database']);

        return $client;
    }

    public function boot(): void
    {
        $this->publishes([$this->getConfigPath() => config_path('doctrine-odm.php')], 'config');
    }

    private function getConfigPath(): string
    {
        return __DIR__ . '/../config/doctrine-odm.php';
    }
}
