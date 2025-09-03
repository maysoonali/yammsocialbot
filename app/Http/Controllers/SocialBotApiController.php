<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class SocialBotApiController extends Controller
{
 
   public function defineAPI(array $data){
    

    // find 'event' 
    if (isset($data['event'])) {

       WebhookEvent::create([
    'id' => Str::uuid(),
    'event_type' => $data['event'],
    'raw_payload' => $data,
    'received_at' => now(),
    'message_id' => isset($data['message_id']) && Message::where('id', $data['message_id'])->exists()
        ? $data['message_id']
        : null
]);
        return $data['event'];
    }
   
    // Return null if not found
    return 'unfound';
} 


public function callExtractor(Request $REQUEST)
{
    $apiData =json_decode($REQUEST->input('undifinedAPI'), true);
    
    // Check for JSON errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        return null; // Invalid JSON
    } $REQUEST->input('undifinedAPI'); 

    $event = $this->defineAPI($apiData);

    // Switch based on event type
    switch ($event) {
        case 'message_updated':
           // $this->messageUpdateExtract($apiData);
            return 'messageUpdateExtract($apiData)';
            
        case 'message_created':
            
            return ($this->messageCreateExtract($apiData)) ;
            
        case 'contact_updated':
           // $this->contactUpdateExtract($apiData);
            return 'contactUpdateExtract($apiData)';
            
        case 'conversation_updated':
            //$this->conversationUpdateExtract($apiData);
            return 'conversationUpdateExtract($apiData)';
            
        case 'conversation_status_changed':
            //$this->statChangedExtract($apiData);
            return 'statChangedExtract($apiData)';
            
        case 'conversation_created':
          //  $this->conversationCreateExtract($apiData);
            return 'conversationCreateExtract($apiData)';
            
        case 'contact_created':
           // $this->contactCreateExtract($apiData);
            return 'contactCreateExtract($apiData)';
            
        default:
            /*Log::warning("Unknown event type received: " . $event);
            throw new \InvalidArgumentException("Unknown event type: " . $event);*/
            return $event;
    }
}
public function messageUpdateExtract($apiData)
{
    
    return 'messageUpdateExtract';
}


public function messageCreateExtract($apiData)
{
    try {
        // Helper to generate UUID if numeric
        $uuid = fn($prefix, $id) => is_numeric($id) 
            ? Uuid::uuid5(Uuid::NAMESPACE_DNS, "$prefix-$id")->toString() 
            : $id;

        // Handle Account
        $accountId = $uuid('account', $apiData['account']['id'] ?? null);
        DB::table('accounts')->updateOrInsert(
            ['id' => $accountId],
            ['name' => $apiData['account']['name'] ?? 'Unknown']
        );

        // Handle User (Contact)
        $contact = $apiData['conversation']['meta']['sender'] ?? [];
        $contactId = $uuid('user', $contact['id'] ?? null);
        DB::table('users')->updateOrInsert(
            ['id' => $contactId],
            [
                'account_id'   => $accountId,
                'name'         => $contact['name'] ?? 'Unknown',
                'phone_number' => $contact['phone_number'] ?? null,
            ]
        );

        // Handle Conversation
        $conversation = $apiData['conversation'] ?? [];
        $conversationId = $uuid('conversation', $conversation['id'] ?? null);
        DB::table('conversations')->updateOrInsert(
            ['id' => $conversationId],
            [
                'account_id'  => $accountId,
                'contact_id'  => $contactId,
                'assignee_id' => null,
                'status'      => $conversation['status'] ?? 'pending',
                'channel'     => $conversation['channel'] ?? 'unknown',
                'labels'      => json_encode($conversation['labels'] ?? []),
                'created_at'  => isset($conversation['created_at']) 
                                ? Carbon::parse($conversation['created_at'])
                                : Carbon::now(),
                'updated_at'  => isset($conversation['updated_at']) 
                                ? Carbon::parse($conversation['updated_at'])
                                : Carbon::now(),
            ]
        );

        // Insert Message
        $messageId = $uuid('message', $conversation['messages'][0]['id'] ?? null);
        DB::table('messages')->insert([
            'id'             => $messageId,
            'account_id'     => $accountId,
            'conversation_id'=> $conversationId,
            'sender_id'      => $contactId,
            'sender_type'    => $apiData['sender']['type'] ?? 'bot',
            'message_type'   => $apiData['message_type'] ?? 'outgoing',
            'content'        => $apiData['content'] ?? null,
            'content_type'   => $apiData['content_type'] ?? null,
            'status'         => $conversation['status'] ?? 'sent',
            'private'        => $apiData['private'] ?? false,
            'created_at'     => isset($apiData['created_at']) 
                                ? Carbon::parse($apiData['created_at'])
                                : Carbon::now(),
            'updated_at'     => isset($apiData['updated_at']) 
                                ? Carbon::parse($apiData['updated_at'])
                                : Carbon::now(),
            'payload_exist'  => !empty($apiData['content_attributes']['message_payload']),
        ]);

        // Insert Payload(s)
        $payload = $apiData['content_attributes']['message_payload'] ?? null;
        if ($payload) {
            $mainContent = $payload['content'] ?? [];
            
            // Save main payload
            DB::table('message_payloads')->insert([
                'id'         => Str::uuid()->toString(),
                'message_id' => $messageId,
                'title'      => $mainContent['title'] ?? null,
                'payload'    => json_encode($mainContent['buttons'] ?? []),
                'type'       => $payload['content_type'] ?? null,
                'image_url'  => $mainContent['image'] ?? null,
                'footer'     => $mainContent['footer'] ?? null,
            ]);

            // Save each button individually
            foreach ($mainContent['buttons'] ?? [] as $button) {
                DB::table('message_payloads')->insert([
                    'id'         => Str::uuid()->toString(),
                    'message_id' => $messageId,
                    'title'      => $button['content']['title'] ?? null,
                    'payload'    => $button['content']['payload'] ?? null,
                    'type'       => $button['content_type'] ?? null,
                    'image_url'  => null,
                    'footer'     => null,
                ]);
            }
        }

        return true;
    } catch (\Exception $e) {
        Log::error("Message Create Extract Failed: " . $e->getMessage());
        return "Message Create Extract Failed: " . $e->getMessage();
    }
}

public function contactUpdateExtract($apiData)
{
    // Implement your logic here
    return 'contactUpdateExtract';
}
public function conversationUpdateExtract($apiData)
{
    // Implement your logic here
    return 'conversationUpdateExtract';
}
public function statChangedExtract($apiData)
{
    // Implement your logic here
    return 'statChangedExtract';
}
public function conversationCreateExtract($apiData)
{
    // Implement your logic here
    return 'conversationCreateExtract';
}
public function contactCreateExtract($apiData)
{
    // Implement your logic here
    return 'contactCreateExtract';
}




}