<?php
/**
 *  =================================================================================
 *  Name        :       Relationship.php
 *  Purpose     :       Entity class for the relationships between users (FriendRequests)
 *  Authors     :       Jonas Riemer, Fabian Belli
 *  Last edited :       07.11.2024
 *  =================================================================================
 *  
 *  USAGE       :
 *  Include this file in your PHP script to get access to the Relationsship class.
 *  
 *  EXAMPLE     :
 *  $user = new UserProfile(array());
 *  $user->save();
 *  $user->delete();
 *  $users = UserProfile::getAll();
 *  $user = UserProfile::findByID('user_id');
 *  $user = UserProfile::findByEmail('email');
 *  
 */
namespace entities;
class Notification
{
    private $notification_id;   // Unique Notification ID to identify a notification
    private $type;              // the type of the notification (0 = friend request, 1 = message, 2 = meetup)	
    private $receiver;          // user_id of the receiver
    private $sender;            // user_id of the sender
    private $related_item_id;   // Related Item ID
    private $created_at;        // created_at
    private $seen;              // seen
    private $message;           // message

    public function __construct (array $notification)
    {
        $this->notification_id  = $notification['notification_id'] ?? $this->generateNotificationID();
        $this->type             = $notification['type'] ?? 0;
        $this->receiver         = $notification['receiver'] ?? null;
        $this->sender           = $notification['sender'] ?? null;
        $this->related_item_id  = $notification['related_item_id'] ?? null;
        $this->created_at       = $notification['created_at'] ?? date('Y-m-d H:i:s');
        $this->seen             = $notification['seen'] ?? 0;
        $this->message          = $notification['message'] ?? null;
    }

    public function generateNotificationID(): string
    {
        return uniqid('notification_');
    }

    public function save()
    {
        $notifications = self::getAll();
        $notifications[$this->notification_id] = $this->toArray();
        file_put_contents(NOTIFICATION_STORAGE_FILE, json_encode($notifications, JSON_PRETTY_PRINT));
    }

    public function delete()
    {
        $notifications = self::getAll();
        unset($notifications[$this->notification_id]);
        file_put_contents(NOTIFICATION_STORAGE_FILE, json_encode($notifications, JSON_PRETTY_PRINT));
    }

    public function toArray(): array
    {
        return [
            'notification_id'   => $this->notification_id,
            'type'              => $this->type,
            'receiver'          => $this->receiver,
            'sender'            => $this->sender,
            'related_item_id'   => $this->related_item_id,
            'created_at'        => $this->created_at,
            'seen'              => $this->seen,
            'message'           => $this->message
        ];
    }

    public static function getAll(): array
    {
        if (file_exists(NOTIFICATION_STORAGE_FILE))
        {
            $notifications = json_decode(file_get_contents(NOTIFICATION_STORAGE_FILE), true);
            return $notifications;
        }
        return [];
    }

    public static function findByID(string $notification_id): ?Notification
    {
        $notifications = self::getAll();
        if (isset($notifications[$notification_id]))
        {
            return new Notification($notifications[$notification_id]);
        }
        return null;
    }

    public static function findByReceiver(string $receiver): array
    {
        $notifications = self::getAll();
        $receiver_notifications = [];
        foreach ($notifications as $notification)
        {
            if ($notification['receiver'] == $receiver)
            {
                $receiver_notifications[] = new Notification($notification);
            }
        }
        return $receiver_notifications;
    }

    public static function findByRelatedItem(string $related_item_id, $type)
    {
        $notifications = self::getAll();
        $related_item_notifications = [];
        foreach ($notifications as $notification)
        {
            if ($notification['related_item_id'] == $related_item_id && $notification['type'] == $type)
            {
                $related_item_notifications[] = new Notification($notification);
            }
        }
        return $related_item_notifications;
    }


    // Getter
    public function getNotificationID(){ return $this->notification_id; }
    public function getType(){ return $this->type; }
    public function getReceiver(){ return $this->receiver; }
    public function getSender(){ return $this->sender; }
    public function getRelatedItemID(){ return $this->related_item_id; }
    public function getCreatedAt(){ return $this->created_at; }
    public function getSeen(){ return $this->seen; }
    public function getMessage(){ return $this->message; }
    
    // Setter
    public function setNotificationID($notification_id){ $this->notification_id = $notification_id; }
    public function setType($type){ $this->type = $type; }
    public function setReceiver($receiver){ $this->receiver = $receiver; }
    public function setSender($sender){ $this->sender = $sender; }
    public function setRelatedItemID($related_item_id){ $this->related_item_id = $related_item_id; }
    public function setCreatedAt($created_at){ $this->created_at = $created_at; }
    public function setSeen($seen){ $this->seen = $seen; }
    public function setMessage($message){ $this->message = $message; }

}