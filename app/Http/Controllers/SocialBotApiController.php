<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessagePayload;
use App\Models\WebhookEvent;


class SocialBotApiController extends Controller
{
 
   public function defineAPI(array $data)
   {
        // find 'event' 
        if (isset($data['event'])) {
            return $data['event']; 
        }
        // Return null if not found
        return 'unfound';
    } 


    public function callExtractor(Request $REQUEST)
    {
        $apiData =json_decode($REQUEST->input('undifinedAPI'), true);
        $messageId = null;
        // Check for JSON errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            return "Invalid JSON data: " . json_last_error_msg(); 
        }
        // Determine event type 
        $event = $this->defineAPI($apiData);

        switch ($event) {
            case 'message_updated':
            case 'message_created':
                $this->messageExtract($apiData);
                return ;

            case 'contact_updated':
            case 'contact_created':
                $this->contactExtract($apiData);
                return ;
           
            case 'conversation_updated':
            case 'conversation_status_changed':
            case 'conversation_created':
                $this->conversationExtract($apiData);
                return ; 
            default:
            
                return $event;
        }
           $event = WebhookEvent::create([
        'id' => $eventId,
        'event_type' => $event,
        'raw_payload' => json_encode($apiData),
        'received_at' => now(),
        'message_id' => $messageId,
    ]);

    }

    public function messageExtract($apiData)
    {

        // Ensure Account exists
        $accountId = Uuid::uuid5(Uuid::NAMESPACE_DNS, (string) $apiData['account']['id'])->toString();
        $account = Account::firstOrCreate(
            ['id' => $accountId,],
            [
                'id' => $accountId,
                'name' => $apiData['account']['name'],
            ]
        );


        //  Ensure User (contact or bot) exists
        $userId = Uuid::uuid5(Uuid::NAMESPACE_DNS, (string) $apiData['sender']['id'])->toString();
        $sender = $apiData['sender'];

        $user = \App\Models\User::firstOrCreate(
            ['id' => $userId],
            [
                'id' => $userId,
                'account_id' => $account->id,
                'name' => $sender['name'],
                'phone_number' => $apiData['conversation']['meta']['sender']['phone_number'],
                'email' => $apiData['conversation']['meta']['sender']['email'],
                'is_business' => false,
            ]
        );

        // Ensure Conversation exists
        $conversationId = Uuid::uuid5(Uuid::NAMESPACE_DNS, (string) $apiData['conversation']['id'])->toString();
        $conversationData = $apiData['conversation'];
        $conversation = \App\Models\Conversation::firstOrCreate(
            ['id' => $conversationId],
            [
                'id' => $conversationId,
                'account_id' => $account->id,
                'contact_id' => $user->id,
                'channel' => $conversationData['channel'],
                'status' => $conversationData['status'],
                'labels' => json_encode($conversationData['labels'] ?? []),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // extract message data to be created or updated
        $messageData = $conversationData['messages'][0] ?? $apiData;
        $messageId = Uuid::uuid5(Uuid::NAMESPACE_DNS, (string) $messageData['id'])->toString();

        $message = \App\Models\Message::updateOrCreate(
            ['id' => $messageId],
            [
            'id' => $messageId,
            'account_id' => $account->id,
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => strtolower($messageData['sender_type'] ?? 'contact'),
            'message_type' => $messageData['message_type'],
            'content' => $messageData['content'],
            'content_type' => $messageData['content_type'],
            'status' => $messageData['status'],
            'private' => $messageData['private'],
            'created_at' => date('Y-m-d H:i:s', $messageData['created_at']),
            'updated_at' => $messageData['updated_at'],
            'payload_exist' => !empty($messageData['payload']),
        ]);

        // MessagePayload (if present)
        if (!empty($messageData['payload'])) {
            \App\Models\MessagePayload::updateOrCreate(
                ['id' => Uuid::uuid5(Uuid::NAMESPACE_DNS, (string) $messageData['id'] . '-payload')->toString()],
                [
                'id' => Uuid::uuid5(Uuid::NAMESPACE_DNS, (string) $messageData['id'] . '-payload')->toString(),
                'message_id' => $message->id,
                'title' => $messageData['payload']['title'] ?? null,
                'payload' => $messageData['payload']['body'] ?? null,
                'type' => $messageData['payload']['type'] ?? null,
                'image_url' => $messageData['payload']['image_url'] ?? null,
                'footer' => $messageData['payload']['footer'] ?? null,
            ]);
        }

        return $message;
    }
    
    public function conversationExtract($apiData){
        // Ensure Account exists
        $accountId = Uuid::uuid5(Uuid::NAMESPACE_DNS, (string) $apiData['messages'][0]['account_id'])->toString();
        $account = Account::firstOrCreate(
            ['id' => $accountId,],
            [
                'id' => $accountId,
                'name' => "Unknown Account",
            ]
        );
        //  Ensure User (contact or bot) exists
        $userId = Uuid::uuid5(Uuid::NAMESPACE_DNS, (string) $apiData['sender']['id'])->toString();
        $user = \App\Models\User::firstOrCreate(
            ['id' => $userId],
            [
                'id' => $userId,
                'account_id' => $account->id,
                'name' => $apiData['name'],
                'phone_number' => $apiData['phone_number'],
                'email' => $apiData['conversation']['meta']['sender']['email'],
                'is_business' => false,
            ]
        );

        $conversationId = Uuid::uuid5(Uuid::NAMESPACE_DNS, (string) $apiData['id'])->toString();      
        $contactId = Uuid::uuid5(Uuid::NAMESPACE_DNS, (string) $apiData['contact_inbox']['contact_id'])->toString();      

        $conversation = \App\Models\Conversation::updateOrCreate(
            ['id' => $conversationId],
            [
                'id' => $conversationId,
                'account_id' => $account->id,
                'contact_id' => $contactId,
                'channel' => $apiData['channel'],
                'status' => $apiData['status'],
                'labels' => json_encode($apiData['labels'] ?? []),
                
            ]
        );    return $conversation;

    }
        
    public function contactExtract($apiData)
    {
        // Ensure Account exists
        $accountId = Uuid::uuid5(Uuid::NAMESPACE_DNS, (string) $apiData['account']['id'])->toString();
        $account = Account::firstOrCreate(
            ['id' => $accountId,],
            [
                'id' => $accountId,
                'name' => $apiData['account']['name'],
            ]
        );
        //User (contact) creation or update
        $userId = Uuid::uuid5(Uuid::NAMESPACE_DNS, (string) $apiData['id'])->toString();
        $user = \App\Models\User::updateOrCreate(
            ['id' => $userId],
            [
                'id' => $userId,
                'account_id' => $accountId,
                'name' => $apiData['name'],
                'phone_number' => $apiData['phone_number'],
                'email' => $apiData['email'],
                'is_business' => false,
            ]
        );
        return ;
    }

}
