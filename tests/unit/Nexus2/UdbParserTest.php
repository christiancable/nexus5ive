<?php

namespace Tests\Unit\Nexus2;

use App\Nexus2\UdbParser;
use RuntimeException;
use Tests\TestCase;

class UdbParserTest extends TestCase
{
    private string $fixturesPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixturesPath = __DIR__.'/fixtures';
    }

    public function test_parse_returns_expected_string_fields(): void
    {
        $parser = new UdbParser($this->fixturesPath.'/test_user.udb');
        $data = $parser->parse();

        $this->assertEquals('TestUser', $data['Nick']);
        $this->assertEquals('T.USER1', $data['UserID']);
        $this->assertEquals('Test User', $data['RealName']);
        $this->assertEquals('A witty tagline', $data['PopName']);
        $this->assertEquals('hashedpw', $data['Password']);
        $this->assertEquals('Computer Science', $data['Dept']);
        $this->assertEquals('Engineering', $data['Faculty']);
        $this->assertEquals('Mon 1/1/97 at 09:00:00', $data['Created']);
        $this->assertEquals('Fri 5/6/99 at 17:30:00', $data['LastOn']);
        $this->assertEquals('HISTORY.3', $data['HistoryFile']);
    }

    public function test_parse_returns_expected_numeric_fields(): void
    {
        $parser = new UdbParser($this->fixturesPath.'/test_user.udb');
        $data = $parser->parse();

        $this->assertEquals(128, $data['Rights']);
        $this->assertEquals(1234, $data['TotalEdits']);
        $this->assertEquals(5678, $data['TimeOn']);
        $this->assertEquals(42, $data['TimesOn']);
        $this->assertEquals(42, $data['UserNo']);
    }

    public function test_rights_label_decoding(): void
    {
        $parser = new UdbParser($this->fixturesPath.'/test_user.udb');
        $data = $parser->parse();

        $this->assertEquals('Moderator', $data['RightsLabel']);
    }

    public function test_sysop_rights_label(): void
    {
        $parser = new UdbParser($this->fixturesPath.'/test_sysop.udb');
        $data = $parser->parse();

        $this->assertEquals('Sysop', $data['RightsLabel']);
        $this->assertEquals(255, $data['Rights']);
    }

    public function test_flag_decoding(): void
    {
        $parser = new UdbParser($this->fixturesPath.'/test_user.udb');
        $data = $parser->parse();

        $this->assertEquals('Male', $data['Sex']);
        $this->assertEquals('None (visible)', $data['Hide']);
        $this->assertEquals('24h', $data['TimeMode']);
        $this->assertEquals('Enabled', $data['Chat']);
        $this->assertEquals('Enabled', $data['Message']);
        $this->assertEquals('Enabled', $data['CommentFlag']);
        $this->assertEquals('Enabled', $data['Mail']);
        $this->assertEquals('Yes', $data['Validated']);
        $this->assertEquals('No', $data['SeeAll']);
    }

    public function test_privilege_bitfield_decoding(): void
    {
        $parser = new UdbParser($this->fixturesPath.'/test_user.udb');
        $data = $parser->parse();

        $this->assertContains('Expel', $data['PrivLabels']);
        $this->assertContains('View Logs', $data['PrivLabels']);
        $this->assertCount(2, $data['PrivLabels']);
    }

    public function test_sysop_has_all_privileges(): void
    {
        $parser = new UdbParser($this->fixturesPath.'/test_sysop.udb');
        $data = $parser->parse();

        $this->assertContains('Sysop', $data['PrivLabels']);
        $this->assertContains('Expel', $data['PrivLabels']);
        $this->assertContains('Star Send', $data['PrivLabels']);
        $this->assertContains('Down BBS', $data['PrivLabels']);
        $this->assertContains('View Logs', $data['PrivLabels']);
        $this->assertCount(8, $data['PrivLabels']);
    }

    public function test_ban_bitfield_decoding(): void
    {
        $parser = new UdbParser($this->fixturesPath.'/test_user.udb');
        $data = $parser->parse();

        $this->assertContains('Mail', $data['BanLabels']);
        $this->assertCount(1, $data['BanLabels']);
    }

    public function test_max_logins_field(): void
    {
        $parser = new UdbParser($this->fixturesPath.'/test_user.udb');
        $data = $parser->parse();

        $this->assertEquals(3, $data['MaxLogins']);
    }

    public function test_see_all_flag(): void
    {
        $parser = new UdbParser($this->fixturesPath.'/test_sysop.udb');
        $data = $parser->parse();

        $this->assertEquals('Yes', $data['SeeAll']);
    }

    public function test_throws_exception_for_missing_file(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File not found');

        new UdbParser('/nonexistent/file.udb');
    }

    public function test_throws_exception_for_wrong_file_size(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'udb');
        file_put_contents($tmpFile, str_repeat("\0", 100));

        try {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('Expected 527 bytes, got 100');

            new UdbParser($tmpFile);
        } finally {
            unlink($tmpFile);
        }
    }
}
