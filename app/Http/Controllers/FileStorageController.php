<?php

namespace App\Http\Controllers;

use App\ErrorLog;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Exception;

class FileStorageController extends Controller {

    private $adapter, $fileSystem;

    public function __construct()

    {
//        $client = new S3Client([
//            'credentials' => [
//                'key'    => env('DO_SPACES_KEY'),
//                'secret' => env('DO_SPACES_SECRET'),
//            ],
//            'region' => env('DO_SPACES_REGION'),
//            'version' => 'latest|version',
//            'endpoint' => env('DO_SPACES_ENDPOINT'),
//            ]);
//
//        $this->adapter = new AwsS3Adapter($client, env('DO_SPACES_BUCKET'));
//        $this->fileSystem = new Filesystem($this->adapter);

    }

    public function testFile () {
        try {
            $path = config('app.env') . '/' . date('Y-m-d') . '.json'; // local/2019-03-01.json
            Storage::disk('spaces')->put($path, json_encode(['foo' => 'bar']));
        } catch ( Exception $e ) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();

            $errorMsg->save();
        }
    }
}
