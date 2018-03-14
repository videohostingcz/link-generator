<?php
namespace Videohostingcz\Tests;

use Videohostingcz\LinkGenerator;

/**
 * This is the LinkGenerator tester.
 */
class LinkGeneratorTest extends \PHPUnit_Framework_TestCase
{

    /** @var LinkGenerator Generator */
    private $generator;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->generator = new LinkGenerator();
    }

    /**
     * Generate URL test.
     */
    public function testGenerator()
    {
        // view link to public file (no parameters)
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4');
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4', $url);

        // download link to public file
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['filename' => 'myvideo.mp4']);
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4?filename=myvideo.mp4', $url);

        // view link to public file with token
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['token' => '9f4a6a71499']);
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4?token=9f4a6a71499', $url);

        // download link to public file with token
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['token' => '9f4a6a71499', 'filename' => 'myvideo.mp4']);
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4?token=9f4a6a71499&filename=myvideo.mp4', $url);

        // view link to private file with value of expires
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['token' => '9f4a6a71499', 'expires' => '1466436357']);
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4?token=9f4a6a71499&expires=1466436357', $url);

        // view link to private file with value of limitsize in bytes
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['token' => '9f4a6a71499', 'limitsize' => '146643']);
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4?token=9f4a6a71499&limitsize=146643', $url);

        // view link to private file with value of limitsize and limitid
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['token' => '9f4a6a71499', 'limitsize' => '146643', 'limitid' => 123456]);
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4?token=9f4a6a71499&limitsize=146643&limitid=123456', $url);

        // view link to private file with value of rate in bytes
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['token' => '9f4a6a71499', 'rate' => '1463']);
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4?token=9f4a6a71499&rate=1463', $url);

        // view link to private file with value of rate in kilobytes (suffix 'k')
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['token' => '9f4a6a71499', 'rate' => '150k']);
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4?token=9f4a6a71499&rate=150k', $url);

        // download link to private file with value of IPv4
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['ip' => '127.0.0.1']);
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4?ip=127.0.0.1', $url);

        // download link to private file with value of IPv4 with site mask
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['ip' => '127.0.0.1', 'ipm' => 32]);
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4?ip=127.0.0.1&ipm=32', $url);

        // download link to private file with value of IPv6
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['ip' => '2001:0db8:0:0:0:0:1428:57ab']);
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4?ip=2001%3A0db8%3A0%3A0%3A0%3A0%3A1428%3A57ab', $url);
        
        // download link to private file with value of IPv6 with site mask
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['ip' => '2001:0db8:0:0:0:0:1428:57ab', 'ipm' => 128]);
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4?ip=2001%3A0db8%3A0%3A0%3A0%3A0%3A1428%3A57ab&ipm=128', $url);

        // view link to private file with sparams value
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['token' => '9f4a6a71499', 'rate' => '160k', 'sparams' => 'token,rate']);
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4?token=9f4a6a71499&rate=160k&sparams=token%2Crate&signature=6112c9ed34d97853c8c2333cec0d424e0a7089f1', $url);

        // view link to private file with sparams value
        $url = $this->generator->generate('s1.cdn.cz/wq5UXbiW.mp4', ['token' => '9f4a6a71499', 'rate' => '160k', 'sparams' => 'rate,token']);
        $this->assertSame('https://s1.cdn.cz/wq5UXbiW.mp4?token=9f4a6a71499&rate=160k&sparams=rate%2Ctoken&signature=e76c158945c683947291f6bcdf61eda85fe60202', $url);
        
        // view link to private file with sparams value path
        $url = $this->generator->generate('s1.cdn.cz/33/LezTxJHdxSgXUeks84u12fvyHoAtP6sGQt0YUdyCLYF6gH7MTKDSnGfVkFPzSyLTnKs52ULviJVCBEhKJhqPUEaUL3s65fwVTP9S4lTzvhe5C9vHM2tV8U8ds4MSaoCKnTqNsPYIg2cle.mp4', ['sparams' => 'path']);
        $this->assertSame('https://s1.cdn.cz/33/LezTxJHdxSgXUeks84u12fvyHoAtP6sGQt0YUdyCLYF6gH7MTKDSnGfVkFPzSyLTnKs52ULviJVCBEhKJhqPUEaUL3s65fwVTP9S4lTzvhe5C9vHM2tV8U8ds4MSaoCKnTqNsPYIg2cle.mp4?sparams=path&signature=8dea03fb714a8b3f748407f3eb9aa816470c271d', $url);
    }

    /**
     * Use undefined parameter test.
     *
     * @expectedException \Videohostingcz\LinkGeneratorException
     */
    public function testUndefinedParameter()
    {
        $this->generator->generate('foo', ['undefined' => 'value']);
    }

    /**
     * Check parametr 'expires' to non numeric value test.
     *
     * @expectedException \Videohostingcz\LinkGeneratorException
     */
    public function testParameterExpiresBadValue()
    {
        $this->generator->generate('foo', ['expires' => '123wsl']);
    }

    /**
     * Check parametr 'size' with invalid charakters test.
     *
     * @expectedException \Videohostingcz\LinkGeneratorException
     */
    public function testParameterSizeBadValue()
    {
        $this->generator->generate('foo', ['size' => '123w']);
    }

    /**
     * Check parametr 'size' with invalid percent value test.
     *
     * @expectedException \Videohostingcz\LinkGeneratorException
     */
    public function testParameterSizeBadPercentValue()
    {
        $this->generator->generate('foo', ['size' => '123p']);
    }

    /**
     * Check parametr 'limit' to non numeric value test.
     *
     * @expectedException \Videohostingcz\LinkGeneratorException
     */
    public function testParameterLimitBadValue()
    {
        $this->generator->generate('foo', ['limit' => '1w23l']);
    }

    /**
     * Check parametr 'ip' to non ip value test.
     *
     * @expectedException \Videohostingcz\LinkGeneratorException
     */
    public function testParameterIpBadValue()
    {
        $this->generator->generate('foo', ['ip' => '123.273.24.54']);
    }

    /**
     * Check parametr 'ipm' with big value for IPv4 test.
     *
     * @expectedException \Videohostingcz\LinkGeneratorException
     */
    public function testParameterIpmIpv4BigValue()
    {
        $this->generator->generate('foo', ['ip' => '127.0.0.1', 'ipm' => '120']);
    }

    /**
     * Check parametr 'ipm' with big value for IPv6 test.
     *
     * @expectedException \Videohostingcz\LinkGeneratorException
     */
    public function testParameterIpmIpv6BigValue()
    {
        $this->generator->generate('foo', ['ip' => '2001:0db8:0:0:0:0:1428:57ab', 'ipm' => '146']);
    }

    /**
     * Check parametr 'ipm' without value of parameter 'ip' test.
     *
     * @expectedException \Videohostingcz\LinkGeneratorException
     */
    public function testParameterIpmWithoutIp()
    {
        $this->generator->generate('foo', ['ipm' => '24']);
    }

    /**
     * Check parametr 'sparams' with invalid value test.
     *
     * @expectedException \Videohostingcz\LinkGeneratorException
     */
    public function testParameterSparamsWithInvalidValue()
    {
        $this->generator->generate('foo', ['sparams' => '1w23l, d343dd']);
    }

    /**
     * Check parametr 'sparams' with invalid property name test.
     *
     * @expectedException \Videohostingcz\LinkGeneratorException
     */
    public function testParameterSparamsWithInvalidPropertyName()
    {
        $this->generator->generate('foo', ['limit' => 125, 'sparams' => 'limit,count']);
    }

    /**
     * Check parametr 'sparams' without value of specific property.
     *
     * @expectedException \Videohostingcz\LinkGeneratorException
     */
    public function testParameterSparamsWithoutPropertyValue()
    {
        $this->generator->generate('foo', ['sparams' => 'limit']);
    }
}
