<?php

namespace App\Listeners\Vk;

use App\Events\Vk\GetGroupPosts;
use App\Library\VKAPI;
use App\Models\VkPosts;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GetGroupPostsListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
//
    }

    protected array  $russianMonths = [
        0 => 'Января',
        1 => 'Февраля',
        2 => 'Марта',
        3 => 'Апреля',
        4 => 'Мая',
        5 => 'Июня',
        6 => 'Июля',
        7 => 'Августа',
        8 => 'Сентября',
        9 => 'Октября',
        10 => 'Ноября',
        11 => 'Декабря',
    ];

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(GetGroupPosts $event)
    {
        $ownerId = $event->ownerId;
        $token = $event->token;
        $count = $event->count;


        $vkApi = new VKAPI($ownerId, $token);
        $response = $vkApi->getPosts($count)['response'];
        $posts = $response['items'];

        VkPosts::truncate();
        foreach ($posts as $post) {
            try {
                DB::beginTransaction();
                $imageUrl = null;
                $title = null;
                if (isset($post['attachments'])) {
                    $isImagesExists = false;
                    foreach($post['attachments'] as $attachment) {
                        if ($attachment['type'] === 'photo') {
                            $photo = $attachment['photo'];
                            $sizesCount = count($photo['sizes']);
                            $lastSizeIdx = $sizesCount - 1;
                            $imageUrl = $photo['sizes'][$lastSizeIdx]['url'];
                            $isImagesExists = true;
                            break;
                        }
                    }
                    if (!$isImagesExists) continue;
                } else {
                    continue;
                }
                $text = $post['text'];
                $title = preg_split ('/\n/', $text)[0];
                $content = isset($title) && $title !== '' ? explode($title, $text)[1] : null;
                $strippedContent = Str::limit($content, 100);
                $dateData = Carbon::parse($post['date'])->format('d-m');
                $dateArray = explode('-', $dateData);
                $days = $dateArray[0];
                $month = +$dateArray[1];
                $monthToString = '';
                foreach($this->russianMonths as $key=>$russianMonth) {
                    if ($month === $key) {
                        $monthToString = $russianMonth;
                        break;
                    }
                }
                $date = $days . " " . $monthToString;
                VkPosts::create([
                    'title' => $title,
                    'content' => $strippedContent,
                    'image_url' => $imageUrl,
                    'views' => $post['views']['count'],
                    'created_time' => $date,
                ]);

                DB::commit();
            } catch (\Exception $error) {
                DB::rollBack();
                dd($error->getMessage());
            }
        }
    }
}
