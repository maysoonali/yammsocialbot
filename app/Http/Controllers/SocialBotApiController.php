<?php

namespace App\Http\Controllers;

use App\Models\WebhookEvent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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
            //$this->messageCreateExtract($apiData);
            return 'messageCreateExtract($apiData)';
            
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
        // extract message data from webhook
        $messageData = [
            'id' => $apiData['message']['id'] ?? null,
            'account_id' => $apiData['account']['id'] ?? null,
            'conversation_id' => $apiData['conversation']['id'] ?? null,
            'sender_id' => $apiData['message']['sender']['id'] ?? null,
            'sender_type' => $apiData['message']['sender']['type'] ?? null,
            'message_type' => $apiData['message']['type'] ?? null,
            'content' => $apiData['message']['content'] ?? null,
            'content_type' => $apiData['message']['content_type'] ?? null,
            'status' => $apiData['message']['status'] ?? null,
            'private' => $apiData['message']['private'] ?? false,
            'created_at' => $apiData['message']['created_at'] ?? now(),
            'updated_at' => $apiData['message']['updated_at'] ?? now(),
            'payload_exist' => isset($apiData['message']['payload']) ? true : false
        ];

        // create the message
        $message = Message::create($messageData);

        // if there's payload data, store it too
        if (isset($apiData['message']['payload'])) {
            MessagePayload::create([
                'message_id' => $message->id,
                'payload' => $apiData['message']['payload']
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Message created successfully',
            'message_id' => $message->id
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to create message: ' . $e->getMessage()
        ], 500);
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
