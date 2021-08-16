<?php
declare(strict_types=1);

namespace Rosamarsky\LaravelDoctrineOdm\Providers;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Types\Type;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use MongoDB\Client;
use Rosamarsky\LaravelDoctrineOdm\Types\CarbonDateType;

class DocumentManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $annotations = glob(__DIR__ . '/../../vendor/doctrine/mongodb-odm/lib/Doctrine/ODM/MongoDB/Mapping/Annotations/*\.php');

        foreach ($annotations as $annotation) {
            if (strpos('Abstract', $annotation)) {
                continue;
            }

            AnnotationRegistry::registerFile($annotation);
        }

        $this->app->bind(DocumentManager::class, function (Container $app) {
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
        return __DIR__ . '/../../config/doctrine-odm.php';
    }
}
