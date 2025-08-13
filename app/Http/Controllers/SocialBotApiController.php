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
    // Implement your logic here
    return 'messageCreateExtract';
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