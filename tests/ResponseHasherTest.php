<?php

namespace codicastudio\responsecache\Test;

use Illuminate\Http\Request;
use Mockery;
use codicastudio\responsecache\CacheProfiles\CacheProfile;
use codicastudio\responsecache\Hasher\DefaultHasher;

class ResponseHasherTest extends TestCase
{
    protected DefaultHasher $requestHasher;

    protected CacheProfile $cacheProfile;

    protected Request $request;

    public function setUp(): void
    {
        parent::setUp();

        $this->cacheProfile = Mockery::mock(CacheProfile::class);

        $this->request = Request::create('https://codicastudiodev');

        $this->requestHasher = new DefaultHasher($this->cacheProfile);
    }

    /** @test */
    public function it_can_generate_a_hash_for_a_request()
    {
        $this->cacheProfile->shouldReceive('useCacheNameSuffix')->andReturn('cacheProfileSuffix');

        $this->assertEquals('responsecache-467d6e9cb7425ed9d3e114e44eb7117f',
            $this->requestHasher->getHashFor($this->request));
    }

    /** @test */
    public function it_generates_a_different_hash_per_request_host()
    {
        $this->cacheProfile->shouldReceive('useCacheNameSuffix')->andReturn('cacheProfileSuffix');

        $request = Request::create('https://codicastudiodev/example-page');
        $requestForSubdomain = Request::create('https://de.codicastudiodev/example-page');

        $this->assertNotEquals($this->requestHasher->getHashFor($request),
            $this->requestHasher->getHashFor($requestForSubdomain));
    }
}
