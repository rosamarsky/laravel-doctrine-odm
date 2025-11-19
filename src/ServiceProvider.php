<?php
declare(strict_types=1);

namespace Rosamarsky\LaravelDoctrineOdm;

use Doctrine\ODM\MongoDB\Mapping\Driver\AttributeDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\SimplifiedXmlDriver;

use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Types\Type;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Rosamarsky\LaravelDoctrineOdm\Types\CarbonDateType;
use MongoDB\Client;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DocumentManager::class, function () {
            $config = new Configuration();
            if (! $configs = $this->app->make('config')->get('doctrine-odm.manager')) {
                throw new \Exception('No config file provided for doctrine-odm.');
            }

            $config->setProxyDir($configs['proxy_dir']);
            $config->setProxyNamespace($configs['proxy_namespace']);
            $config->setAutoGenerateProxyClasses($configs['auto_generate_proxy_classes']);

            $config->setHydratorDir($configs['hydrator_dir']);
            $config->setHydratorNamespace($configs['hydrator_namespace']);
            $config->setAutoGenerateHydratorClasses($configs['auto_generate_hydrator_classes']);

            $config->setPersistentCollectionDir($configs['persistent_collection_dir']);
            $config->setPersistentCollectionNamespace($configs['persistent_collection_namespace']);
            $config->setAutoGeneratePersistentCollectionClasses($configs['auto_generate_persistent_collection_classes']);

            $config->setUseTransactionalFlush($configs['use_transactional_flush']);

            $config->setDefaultDB($configs['default_db']);

            $driver = match ($configs['driver'] ?? 'attributes') {
                'xml' => new SimplifiedXmlDriver($configs['metadata_dir']),
                default => new AttributeDriver($configs['metadata_dir']),
            };
            $config->setMetadataDriverImpl($driver);

            Type::registerType(CarbonDateType::CARBON, CarbonDateType::class);

            return DocumentManager::create($this->resolveMongoClient($configs['connection']), $config);
        });
    }

    protected function resolveMongoClient(array $connection): Client
    {
        $client = new Client(
            $connection['dsn'],
            $connection['auth'] ?? [],
            $connection['options'] ?? [],
        );

        if (! $connection['auth']['database']) {
            $client->selectDatabase($connection['auth']['database']);
        }

        return $client;
    }

    public function boot(): void
    {
        $this->publishes([
            $this->getConfigPath() => config_path('doctrine-odm.php'),
        ], 'config');
    }

    private function getConfigPath(): string
    {
        return __DIR__ . '/../config/doctrine-odm.php';
    }
}
