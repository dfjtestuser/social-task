<?php

namespace App\Console\Commands;

use App\SocialAccount;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Post;
use App\User;

class FetchData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facebook:fetchdata {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Megpróbálja lehúzni a posztokat adott usernél';

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
        $user = User::find($this->argument('user'));
        $fields="created_time,caption,description,link,full_picture,picture,message,attachments";
        @unlink(storage_path()."/app/proc/".$user->id);
        try {
            $status = ['time' => time(), 'status' => 'processing'];
            file_put_contents( storage_path()."/app/proc/".$user->id.".json",json_encode($status) );
            $since = ($user->lastPost()) ? $user->lastPost()->created_time : '2005-01-01';
            $facebook_account = SocialAccount::whereProvider('facebook')->whereUserId($user->id)->first();
            $access_token = $facebook_account->access_token;
            $client = new Client;
            $r = $client->request('GET',"https://graph.facebook.com/v2.7/me/feed?fields={$fields}&limit=10000&since={$since}&access_token=".$access_token);

            $result = json_decode($r->getBody(),1);
            $my_posts = $result['data'];
            if(sizeof($my_posts)==0) {
                $status = ['time' => time(), 'status' => 'done'];
                file_put_contents( storage_path()."/app/proc/".$user->id.".json",json_encode($status) );
                return;
            }
            while(array_key_exists("paging", $result) && array_key_exists("next", $result["paging"]) && sizeof($result['data'] > 0)) {
                $r = $client->request('GET',$result['paging']['next']);
                $result = json_decode($r->getBody(),1);
                $my_posts = array_merge($my_posts, $result['data']);
                if(isset($result['paging'])) {
                    $this->line($result['data'][0]['created_time']);
                }
            }
            \DB::raw("SET names utf8");
            foreach($my_posts as $post) {
                $post['attachments'] = isset($post['attachments']) ? json_encode($post['attachments']) : null;
                $post['user_id'] = $user->id;
                $post['created_time'] = date("Y-m-d H:i:s",strtotime($post['created_time']));
                $post = json_decode(json_encode($post),1);
                if(!Post::find($post['id'])) {
                    try {
                        Post::create($post);
                    } catch(\Illuminate\Database\QueryException $e) {
                        print_r($post);
                        dd($e->getMessage());


                    }
                }
            }
            $status = ['time' => time(), 'status' => 'done'];
            file_put_contents( storage_path()."/app/proc/".$user->id.".json",json_encode($status) );
            return;
        } catch(\Exception $e) {
            $status = ['time' => time(), 'status' => 'error', 'message' => $e->getMessage()];
            file_put_contents( storage_path()."/app/proc/".$user->id.".json",json_encode($status) );
            return;
        }


    }
}
