<?php

namespace Drupal\Tests\handlebars_theme_handler\Kernel;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ExtensionList;
use Drupal\Core\PhpStorage\PhpStorageFactory;
use Drupal\handlebars_theme_handler\FilesUtility;
use Drupal\handlebars_theme_handler\Templating\Renderer;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests Renderer class.
 *
 * @group handlebars_theme_handler
 */
class RendererKernelTest extends KernelTestBase {

  /**
   * Tests handlebars templates rendering.
   */
  public function testRender() {
    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $extensionList = $this->createMock(ExtensionList::class);
    $phpStorage = PhpStorageFactory::get('handlebars_test');
    $themeConfig = $this->createMock(Config::class);
    $themeConfig->expects($this->once())
      ->method('get')
      ->with('default')
      ->willReturn('default_theme');
    $configFactory->expects($this->once())
      ->method('get')
      ->with('system.theme')
      ->willReturn($themeConfig);

    $extensionList->expects($this->once())
      ->method('getPathname')
      ->with('default_theme')
      ->willReturn(__DIR__ . '/../../info.yml');

    $renderer = new Renderer(new FilesUtility(), $configFactory, $extensionList, $phpStorage);

    $result = $renderer->render('test.hbs', ['name' => 'World']);
    $this->assertTrue($phpStorage->exists('test.hbs'));
    $this->assertEquals($result, '<p>Hello World!</p>');
  }

}
