<?php

declare(strict_types=1);

namespace Vasoft\Core\Updater;

use Bitrix\Main\IO\FileSystemEntry;
use Bitrix\Main\Loader;
use Vasoft\MockBuilder\Mocker\MockDefinition;
use PHPUnit\Framework\TestCase;
use Bitrix\Main\IO\Directory;

include_once __DIR__ . '/MockTrait.php';

/**
 * @coversDefaultClass \Vasoft\Core\Updater\FileInstaller
 *
 * @internal
 */
final class FileInstallerTest extends TestCase
{
    use MockTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initMocks();
        Directory::cleanMockData('__construct');
        $this->clearMockFilePutContents();
        $this->clearMockUnlink();
        $this->clearMockFilePutContents();
    }

    public function testCheckAdminPagesRenew(): void
    {
        $this->clearMockRealPath([
            '/www//bitrix/modules/vendor_module/admin/' => '/www/bitrix/modules/vendor_module/admin/',
            '/www//bitrix/admin/' => '/www/bitrix/admin/',
        ]);
        Loader::cleanMockData('getDocumentRoot', defaultDefinition: new MockDefinition(result: '/www/'));
        Directory::cleanMockData('isExists', defaultDefinition: new MockDefinition(result: true));
        $sourceFile1 = self::createMock(FileSystemEntry::class);
        $sourceFile1->expects(self::once())->method('isFile')->willReturn(true);
        $sourceFile1->expects(self::once())->method('getName')->willReturn('file1.php');
        $sourceFile1->expects(self::never())->method('getPhysicalPath');

        $sourceFile2 = self::createMock(FileSystemEntry::class);
        $sourceFile2->expects(self::once())->method('isFile')->willReturn(true);
        $sourceFile2->expects(self::once())->method('getName')->willReturn('file3.php');
        $sourceFile2->expects(self::never())->method('getPhysicalPath');

        $sourceSubDir = self::createMock(FileSystemEntry::class);
        $sourceSubDir->expects(self::once())->method('isFile')->willReturn(false);
        $sourceSubDir->expects(self::never())->method('getName');
        $sourceSubDir->expects(self::never())->method('getPhysicalPath');

        $destinationFile1 = self::createMock(FileSystemEntry::class);
        $destinationFile1->expects(self::once())->method('isFile')->willReturn(true);
        $destinationFile1->expects(self::once())->method('getName')->willReturn('vendor_module_file1.php');
        $destinationFile1->expects(self::once())->method('getPhysicalPath')->willReturn(
            '/www/bitrix/admin/vendor_module_file1.php',
        );
        $destinationFile2 = self::createMock(FileSystemEntry::class);
        $destinationFile2->expects(self::once())->method('isFile')->willReturn(true);
        $destinationFile2->expects(self::once())->method('getName')->willReturn('vendor_module_file2.php');
        $destinationFile2->expects(self::once())->method('getPhysicalPath')->willReturn(
            '/www/bitrix/admin/vendor_module_file2.php',
        );
        Directory::cleanMockData('getChildren', [
            new MockDefinition(result: [$sourceFile1, $sourceFile2, $sourceSubDir]),
            new MockDefinition(result: [$destinationFile1, $destinationFile2]),
        ]);

        $installer = new FileInstaller('vendor_module_', '/www/bitrix/modules/vendor_module/');
        $installer->checkAdminPages(true);
        self::assertSame(2, Directory::getMockedCounter('getChildren'));
        self::assertSame(2, Directory::getMockedCounter('__construct'));
        self::assertSame(
            ['/www/bitrix/modules/vendor_module/admin/', null],
            Directory::getMockedParams('__construct', 0),
        );
        self::assertSame(['/www/bitrix/admin/', null], Directory::getMockedParams('__construct', 1));
        self::assertSame(1, self::$mockUnlinkCount);
        self::assertSame('/www/bitrix/admin/vendor_module_file2.php', self::$mockUnlinkParam[0]);
        self::assertSame(2, self::$mockFilePutContentsCount);
        self::assertSame('/www//bitrix/admin/vendor_module_file1.php', self::$mockFilePutContentsParam[0]);
        self::assertSame('/www//bitrix/admin/vendor_module_file3.php', self::$mockFilePutContentsParam[1]);
        self::assertSame(
            '<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/vendor_module/admin/file1.php");',
            self::$mockFilePutContentsContent[0],
        );
        self::assertSame(
            '<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/vendor_module/admin/file3.php");',
            self::$mockFilePutContentsContent[1],
        );
    }

    public function testCheckAdminPages(): void
    {
        $this->clearMockRealPath([
            '/www//bitrix/modules/vendor_module/admin/' => '/www/bitrix/modules/vendor_module/admin/',
            '/www//bitrix/admin/' => '/www/bitrix/admin/',
        ]);
        Loader::cleanMockData('getDocumentRoot', defaultDefinition: new MockDefinition(result: '/www/'));
        Directory::cleanMockData('isExists', defaultDefinition: new MockDefinition(result: true));
        $sourceFile1 = self::createMock(FileSystemEntry::class);
        $sourceFile1->expects(self::once())->method('isFile')->willReturn(true);
        $sourceFile1->expects(self::once())->method('getName')->willReturn('file1.php');
        $sourceFile1->expects(self::never())->method('getPhysicalPath');

        $sourceFile2 = self::createMock(FileSystemEntry::class);
        $sourceFile2->expects(self::once())->method('isFile')->willReturn(true);
        $sourceFile2->expects(self::once())->method('getName')->willReturn('file3.php');
        $sourceFile2->expects(self::never())->method('getPhysicalPath');

        $sourceSubDir = self::createMock(FileSystemEntry::class);
        $sourceSubDir->expects(self::once())->method('isFile')->willReturn(false);
        $sourceSubDir->expects(self::never())->method('getName');
        $sourceSubDir->expects(self::never())->method('getPhysicalPath');

        $destinationFile1 = self::createMock(FileSystemEntry::class);
        $destinationFile1->expects(self::once())->method('isFile')->willReturn(true);
        $destinationFile1->expects(self::once())->method('getName')->willReturn('vendor_module_file1.php');
        $destinationFile1->expects(self::once())->method('getPhysicalPath')->willReturn(
            '/www/bitrix/admin/vendor_module_file1.php',
        );
        $destinationFile2 = self::createMock(FileSystemEntry::class);
        $destinationFile2->expects(self::once())->method('isFile')->willReturn(true);
        $destinationFile2->expects(self::once())->method('getName')->willReturn('vendor_module_file2.php');
        $destinationFile2->expects(self::once())->method('getPhysicalPath')->willReturn(
            '/www/bitrix/admin/vendor_module_file2.php',
        );
        Directory::cleanMockData('getChildren', [
            new MockDefinition(result: [$sourceFile1, $sourceFile2, $sourceSubDir]),
            new MockDefinition(result: [$destinationFile1, $destinationFile2]),
        ]);

        $installer = new FileInstaller('vendor_module_', '/www/bitrix/modules/vendor_module/');
        $installer->checkAdminPages();
        self::assertSame(2, Directory::getMockedCounter('getChildren'));
        self::assertSame(2, Directory::getMockedCounter('__construct'));
        self::assertSame(
            ['/www/bitrix/modules/vendor_module/admin/', null],
            Directory::getMockedParams('__construct', 0),
        );
        self::assertSame(['/www/bitrix/admin/', null], Directory::getMockedParams('__construct', 1));
        self::assertSame(1, self::$mockUnlinkCount);
        self::assertSame('/www/bitrix/admin/vendor_module_file2.php', self::$mockUnlinkParam[0]);
        self::assertSame(1, self::$mockFilePutContentsCount);
        self::assertSame('/www//bitrix/admin/vendor_module_file3.php', self::$mockFilePutContentsParam[0]);
        self::assertSame(
            '<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/vendor_module/admin/file3.php");',
            self::$mockFilePutContentsContent[0],
        );
    }

    public function testCheckAdminPagesSourceNotExists(): void
    {
        $this->clearMockRealPath([
            '/www//bitrix/modules/vendor_module/admin/' => '/www/bitrix/modules/vendor_module/admin/',
            '/www//bitrix/admin/' => '/www/bitrix/admin/',
        ]);
        Loader::cleanMockData('getDocumentRoot', defaultDefinition: new MockDefinition(result: '/www/'));
        Directory::cleanMockData('isExists', [
            new MockDefinition(result: false),
            new MockDefinition(result: true),
        ]);
        $destinationFile1 = self::createMock(FileSystemEntry::class);
        $destinationFile1->expects(self::once())->method('isFile')->willReturn(true);
        $destinationFile1->expects(self::once())->method('getName')->willReturn('vendor_module_file1.php');
        $destinationFile1->expects(self::once())->method('getPhysicalPath')->willReturn(
            '/www/bitrix/admin/vendor_module_file1.php',
        );
        $destinationFile2 = self::createMock(FileSystemEntry::class);
        $destinationFile2->expects(self::once())->method('isFile')->willReturn(true);
        $destinationFile2->expects(self::once())->method('getName')->willReturn('vendor_module_file2.php');
        $destinationFile2->expects(self::once())->method('getPhysicalPath')->willReturn(
            '/www/bitrix/admin/vendor_module_file2.php',
        );
        Directory::cleanMockData('getChildren', [
            new MockDefinition(result: [$destinationFile1, $destinationFile2]),
        ]);

        $installer = new FileInstaller('vendor_module_', '/www/bitrix/modules/vendor_module/');
        $installer->checkAdminPages();
        self::assertSame(1, Directory::getMockedCounter('getChildren'));
        self::assertSame(2, Directory::getMockedCounter('__construct'));
        self::assertSame(
            ['/www/bitrix/modules/vendor_module/admin/', null],
            Directory::getMockedParams('__construct', 0),
        );
        self::assertSame(['/www/bitrix/admin/', null], Directory::getMockedParams('__construct', 1));
        self::assertSame(2, self::$mockUnlinkCount);
        self::assertSame('/www/bitrix/admin/vendor_module_file1.php', self::$mockUnlinkParam[0]);
        self::assertSame('/www/bitrix/admin/vendor_module_file2.php', self::$mockUnlinkParam[1]);
    }

    public function testSectionNotExists(): void
    {
        $this->clearMockRealPath([
            '/home/test/www//bitrix/admin/' => '/home/test/www/bitrix/admin/',
            '/bitrix/admin/' => '/home/bitrix/www/bitrix/admin/',
        ]);

        Loader::cleanMockData('getDocumentRoot', defaultDefinition: new MockDefinition(result: '/home/test/www/'));
        Directory::cleanMockData('isExists', defaultDefinition: new MockDefinition(result: false));
        Directory::cleanMockData('getChildren', defaultDefinition: new MockDefinition(result: []));
        Directory::cleanMockData('__construct');

        $installer = new FileInstaller('vendor_module', '/install/on');
        $installer->cleanAdminPages();

        self::assertSame(0, self::$mockUnlinkCount);
        self::assertSame(1, Directory::getMockedCounter('isExists'));
        self::assertSame(0, Directory::getMockedCounter('getChildren'));
        self::assertSame(1, Directory::getMockedCounter('__construct'));
        self::assertSame(['/home/test/www/bitrix/admin/', null], Directory::getMockedParams('__construct', 0));
    }

    public function testClean(): void
    {
        $this->clearMockRealPath([
            '/home/test/www//bitrix/admin/' => '/home/test/www/bitrix/admin/',
            '/bitrix/admin/' => '/home/bitrix/www/bitrix/admin/',
        ]);

        Loader::cleanMockData('getDocumentRoot', defaultDefinition: new MockDefinition(result: '/home/test/www/'));
        Directory::cleanMockData('isExists', defaultDefinition: new MockDefinition(result: true));
        $file1 = self::createMock(FileSystemEntry::class);
        $file1->expects(self::once())->method('isFile')->willReturn(true);
        $file1->expects(self::once())->method('getName')->willReturn('vendor_module_file1.php');
        $file1->expects(self::once())->method('getPhysicalPath')->willReturn(
            '/home/bitrix/www/bitrix/admin/vendor_module_file1.php',
        );
        $file2 = self::createMock(FileSystemEntry::class);
        $file2->expects(self::once())->method('isFile')->willReturn(true);
        $file2->expects(self::once())->method('getName')->willReturn('vendor_module_file2.php');
        $file2->expects(self::once())->method('getPhysicalPath')->willReturn(
            '/home/bitrix/www/bitrix/admin/vendor_module_file2.php',
        );
        $file3 = self::createMock(FileSystemEntry::class);
        $file3->expects(self::once())->method('isFile')->willReturn(false);
        $file3->expects(self::never())->method('getName');
        $file3->expects(self::never())->method('getPhysicalPath');

        Directory::cleanMockData(
            'getChildren',
            defaultDefinition: new MockDefinition(result: [$file1, $file2, $file3]),
        );

        $installer = new FileInstaller('vendor_module', '/install/on');
        $installer->cleanAdminPages();

        self::assertSame(2, self::$mockUnlinkCount);
        self::assertSame('/home/bitrix/www/bitrix/admin/vendor_module_file1.php', self::$mockUnlinkParam[0]);
        self::assertSame('/home/bitrix/www/bitrix/admin/vendor_module_file2.php', self::$mockUnlinkParam[1]);
        self::assertSame(1, Directory::getMockedCounter('isExists'));
        self::assertSame(1, Directory::getMockedCounter('getChildren'));
        self::assertSame(1, Directory::getMockedCounter('__construct'));
        self::assertSame(['/home/test/www/bitrix/admin/', null], Directory::getMockedParams('__construct', 0));
    }
}
