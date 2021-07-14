<?php
/**
 * @file
 * Contains Arocom\arocoml3dmigration\Plugin.
 */


namespace Arocom\arocoml3dmigration;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Plugin\PluginInterface;
use DrupalFinder\DrupalFinder;
use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginEvents;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;


/**
 * Class Handler.
 *
 * @package arocom\arocom-migration
 */

class Plugin implements PluginInterface, EventSubscriberInterface
{
    protected $composer;
    protected $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_CREATE_PROJECT_CMD => 'applyaroscaffs',
            ScriptEvents::POST_INSTALL_CMD => 'applyaroscaffs',
            ScriptEvents::POST_UPDATE_CMD => 'applyaroscaffs',
        ];
    }


    public function getCapabilities()
    {
        return array(
            'Composer\Plugin\Capability\CommandProvider' => 'Arocom\arocoml3dmigration\CommandProvider',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function applyaroscaffs(Event $event): void
    {
        $fs = new Filesystem();
        $drupalFinder = new DrupalFinder();
        $drupalFinder->locateRoot(getcwd());
        $drupalRoot = $drupalFinder->getDrupalRoot();
        $composerRoot = $drupalFinder->getComposerRoot();
        if (!$fs->exists($composerRoot . '.ahoy.yml') || ($fs->exists($composerRoot . '.ahoy.yml'))) {
            copy($composerRoot . '/vendor/arocom/arocom-migration/.ahoy.yml', $composerRoot . '/.ahoy.yml');
        }
        // Make sure that settings.docker.php gets called from settings.php.
        $settingsPath = $composerRoot . '/settings/default';
        $settingsPhpFile = $settingsPath . '/settings.php';
        if ($fs->exists($settingsPhpFile)) {
            $settingsPhp = file_get_contents($settingsPhpFile);
            if (strpos($settingsPhp, 'settings.docker.php') === FALSE) {
                $settingsPhp .= "\n\nif (file_exists('../drupal/sites/default/settings.docker.php')) {\n  include  '../drupal/sites/default/settings.docker.php';\n}\n";
                file_put_contents($settingsPhpFile, $settingsPhp);
            }
        }
        // Append the settings files from the project onto l3d settings files
        for ($i = 1; $i <= 1; $i++) {
            exec("sed -i -e '/<?php/{r ./drupal/sites/default/default.settings.php' -e 'd}' ./settings/default/settings.php");
            exec("sed -i -e '/<?php/{r ./drupal/sites/example.settings.local.php' -e 'd}' ./settings/default/settings.local.php");
            exec("sed -i -e '168d' ./settings/default/settings.local.php");
            exec("sed -i -e 's/i <=/i </' ./vendor/arocom/arocom-migration/src/Plugin.php");
        }
    }
}







