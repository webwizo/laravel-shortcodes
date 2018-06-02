<?php
namespace Webwizo\Shortcodes\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use File;

class CreateShortcode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shortcode:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create New Shortcode';

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
        //
        $name = $this->argument('name');
        $file_path = app_path('Shortcodes'.'/'.$name.'.php');
        if(file_exists( $file_path ) === false){
            //create new shortcode file
            $this->info("Creating new shortcoode ".$name);
            $create_file = File::put($file_path, "<?php\nnamespace App\Shortcodes;\n\nclass ".$name." {\n\tpublic function register(\$shortcode, \$content, \$compiler, \$name, \$viewData)\n\t{\n\t\t//\n\t}\n}");
            if($create_file != false){
                $this->info('File Created');
            }else{
                $this->info('Unable to create file, please create manaully at '.$file_path);
            }
        }else{
            $this->info($name." already exists");
        }
    }
}

