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



/**
 * Class Handler.
 *
 * @package arocom\arocom-migration
 */

class Plugin implements PluginInterface, EventSubscriberInterface{
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
    public static function getSubscribedEvents()
    {
        return array(
            PluginEvents::PRE_FILE_DOWNLOAD => array(
                array('onPreFileDownload', 0)
            ),
        );
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
protected function applyaroscaffs(){
    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(getcwd());
    $drupalRoot = $drupalFinder->getDrupalRoot();
    $composerRoot = $drupalFinder->getComposerRoot();
    if (!$fs->exists($composerRoot . '.ahoy.yml') || ($fs->exists($composerRoot . '.ahoy.yml'))) {
        copy($composerRoot . '/vendor/arocom-migration/.ahoy.yml', $composerRoot . '/.ahoy.yml');
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
    for ($i = 1; $i <= 1; $i++){
        exec("sed -i -e '/<?php/{r ./drupal/sites/default/default.settings.php' -e 'd}' ./settings/default/settings.php");
        exec("sed -i -e '/<?php/{r ./drupal/sites/example.settings.local.php' -e 'd}' ./settings/default/settings.local.php");
        exec("sed -i -e '168d' ./settings/default/settings.local.php");
        exec("sed -i -e 's/i <=/i </' ./scripts/composer/ScriptHandler.php");
    }
}






}
