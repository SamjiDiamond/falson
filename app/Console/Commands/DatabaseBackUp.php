<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\V2\UserController;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:mysql {--command= : <create|local|delete|restore> command to execute} {--snapshot= : provide name of snapshot}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Database Backup';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        switch ($this->option('command')) {
            case 'create':
                $this->takeSnapShot();
                break;

            case 'local':
                $this->takelocalSnapShot();
                break;

            case 'delete':
                $this->deletelocalSnapShots();
                break;

            case 'restore':
                $this->restoreSnapShot();
                break;

            default:
                $this->error("Invalid Option !!");
                break;
        }
    }

    /**
     * Function takes regular backup
     * for mysql database..
     *
     */
    private function takeSnapShot()
    {
        set_time_limit(0);

        $filename = "backup-" . Carbon::now()->format('Y-m-d') . ".sql";
        $storage = storage_path("") . "/db_backup/" . $filename;

        // run the cli job
        $process = new Process('mysqldump -u' . env('DB_USERNAME') . ' -p' . env('DB_PASSWORD') . ' ' . env('DB_DATABASE') . ' > ' . $storage);
        $process->run();

        try {

            if ($process->isSuccessful()) {
                $fbs = new UserController();
                $fbs->uploadFile2FBS($storage, 'database', $filename);

                $this->info("Backup successfully");

//                $s3 = \Storage::disk('local');
//
//                $current_timestamp = time() - (72 * 3600);
//                $allFiles = $s3->allFiles(env('APP_ENV'));
//
//                foreach ($allFiles as $file)
//                {
//                    // delete the files older then 3 days..
//                    if ( $s3->lastModified($file) <= $current_timestamp )
//                    {
//                        $s3->delete($file);
//                        $this->info("File: {$file} deleted.");
//                    }
//                }
            } else {
                throw new ProcessFailedException($process);
            }

//            @unlink($storage);
        } catch (Exception $e) {
            $this->info($e->getMessage());
        }
    }

    /**
     * Function takes regular backup
     * for mysql database..
     *
     */
    private function takelocalSnapShot()
    {
        set_time_limit(0);

        $filename = "backup-" . Carbon::now()->format('Y-m-d-H:i') . ".sql";
        $storage = storage_path("") . "/db_backup/" . $filename;

        // run the cli job
        $process = new Process('mysqldump -u' . env('DB_USERNAME') . ' -p' . env('DB_PASSWORD') . ' ' . env('DB_DATABASE') . ' > ' . $storage);
        $process->run();

        try {

            if ($process->isSuccessful()) {
                $this->info("Local Backup was successfully");
            } else {
                throw new ProcessFailedException($process);
            }

        } catch (Exception $e) {
            $this->info($e->getMessage());
        }
    }

    /**
     * Function delete regular backup
     * for mysql database..
     *
     */
    private function deletelocalSnapShots()
    {
        set_time_limit(0);

        $dbstorage = storage_path("") . "/db_backup/*.sql";

        // run the cli job
        $process = new Process('rm -R ' . $dbstorage);
        $process->run();

        try {

            if ($process->isSuccessful()) {
                $this->info("All backup has been deleted successfully");
            } else {
                throw new ProcessFailedException($process);
            }

        } catch (Exception $e) {
            $this->info($e->getMessage());
        }
    }

    /**
     * Function restore given snapshot
     * for mysql database
     */
    private function restoreSnapShot()
    {
        $snapshot = $this->option('snapshot');
        if (!$snapshot) {
            $this->error("snapshot option is required.");
        }

        $this->info("It has been commented");

//        try {
//
//            // get file from s3
//            $s3 = \Storage::disk('s3');
//            $found = $s3->get('/mysql/' .$snapshot. '.sql');
//            $tempLocation = '/tmp/' .env('DB_DATABASE') . '_' . date("Y-m-d_Hi") . '.sql';
//
//            // create a temp file
//            $bytes_written = File::put($tempLocation, $found);
//            if ($bytes_written === false) {
//                $this->info("Error writing to file: " .$tempLocation);
//            }
//
//            // run the cli job
//            $process = new Process("mysql -h " .env('DB_HOST'). " -u " .env('DB_USERNAME'). " -p" .env('DB_PASSWORD'). " ".env('DB_DATABASE'). " < {$tempLocation}");
//            $process->run();
//
//            //@unlink($tempLocation);
//            if ($process->isSuccessful()) {
//                $this->info("Restored snapshot: " .$snapshot);
//            }
//            else {
//                throw new ProcessFailedException($process);
//            }
//        }
//        catch (\Exception $e) {
//            $this->info('File Not Found: '. $e->getMessage());
//        }
    }
}
