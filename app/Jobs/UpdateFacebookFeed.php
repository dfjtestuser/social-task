<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use GuzzleHttp\Client;
use App\User;
use App\SocialAccount;
use App\Post;

class UpdateFacebookFeed implements  ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->user;
        try {
            print "indul\n";
            $status = ['time' => time(), 'status' => 'processing'];
            file_put_contents( storage_path()."/app/proc/".$user->id.".json",json_encode($status) );
            $since = ($user->lastPost()) ? $user->lastPost()->created_time : '2005-01-01';
            $facebook_account = SocialAccount::whereProvider('facebook')->whereUserId($user->id)->first();
            $access_token = $facebook_account->access_token;
            $client = new Client;
            $r = $client->request('GET',"https://graph.facebook.com/v2.7/me/feed?fields=created_time,caption,picture,message,attachments&limit=10000&since={$since}&access_token=".$access_token);
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
//                    $this->line($result['data'][0]['created_time']);
                }
            }

            foreach($my_posts as $post) {
                $post['attachments'] = isset($post['attachments']) ? json_encode($post['attachments']) : null;
                $post['user_id'] = $user->id;
                $post['created_time'] = date("Y-m-d H:i:s",strtotime($post['created_time']));
                if(!Post::find($post['id'])) {
                    //Post::create($post);
                }
            }
            $status = ['time' => time(), 'status' => 'done'];
            file_put_contents( storage_path()."/app/proc/".$user->id.".json",json_encode($status) );
            print "vege\n";
            return;
        } catch(\Exception $e) {
            $status = ['time' => time(), 'status' => 'error', 'message' => $e->getMessage()];
            file_put_contents( storage_path()."/app/proc/".$user->id.".json",json_encode($status) );
            return;
        }
    }
}
