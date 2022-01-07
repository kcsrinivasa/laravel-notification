![Laravel](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)


# Laravel Generate Notifications

Hi All!

Notifications are informational messages delivered to app users. Laravel provides a built-in feature called Notification. It allows you to send a message to any user via different communication channels. Today, we'll focus on sending email notifications, database and explain how to implement this in your app.

Here is the example focused on regitration notification. when new user registered send notification to all existing users.

This example covered actions for individual logged user can view new/unread notification in nav bar, view all notifications, update individual notification to mark as read, update all notifications to mark as read, and clear all notification.
### Preview
![notification_list](https://github.com/kcsrinivasa/laravel-notification/blob/main/output/notification_list.jpg?raw=true)
![new_notifications](https://github.com/kcsrinivasa/laravel-notification/blob/main/output/new_notifications.jpg?raw=true)
![notification_mail](https://github.com/kcsrinivasa/laravel-notification/blob/main/output/notification_mail.jpg?raw=true)
![notification_details](https://github.com/kcsrinivasa/laravel-notification/blob/main/output/notification_details.jpg?raw=true)



### Step 1: Install Laravel
```bash
composer create-project --prefer-dist laravel/laravel notification
```

### Step 2: Update database and email credentials in .env file
```javascript
DB_DATABASE=laravel_notification
DB_USERNAME=root
DB_PASSWORD=password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=yourmail@gmail.com
MAIL_PASSWORD=yourmailpassword
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=yourmail@gmail.com
```

### Step 3: Install login / registration scaffolding package
```bash
composer require laravel/ui
php artisan ui bootstrap --auth
npm install && npm run dev
```

### Step 4: Create notification
```bash
php artisan make:notification RegisterNotification
```

### Step 5: Create controller
```bash
php artisan make:controller NotificationController -r
```

### Step 6: Create notification table and create all tables in database
```bash
php artisan notifications:table
php artisan migrate
```
### Step 7: Add routes
```bash
Route::get('notifications','App\Http\Controllers\NotificationController@index')->name('notification.index');
Route::get('notifications/{notification}','App\Http\Controllers\NotificationController@show')->name('notification.show');
Route::put('notifications/mark-as-read-all','App\Http\Controllers\NotificationController@markAllAsRead')->name('notification.markAsRead.all');
Route::delete('notifications/clear-all','App\Http\Controllers\NotificationController@destroy')->name('notification.destory');
Route::put('notification/{notification}/mark-as-read','App\Http\Controllers\NotificationController@markAsRead')->name('notification.markAsRead.individual');
Route::get('users/{user}','App\Http\Controllers\HomeController@user')->name('user.show');
```
### Step 8: Add functions in controller
Add below functions in app/Http/Controllers/NotificationController.php
```bash
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = auth()->user()->notifications;
        return view('notification')->withNotifications($notifications);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $notification = auth()->user()->notifications()->where('id',$id)->get()->first(); 
        auth()->user()->unreadNotifications->where('id',$id)->markAsRead();
        $url = isset($notification->data['url'])?url($notification->data['url']):null;
        if($url){ return redirect(url($url));}
         return redirect()->back();
    }
    /**
     * Update the specified user notification in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(Request $request,$id)
    {
         auth()->user()->unreadNotifications->where('id',$id)->markAsRead();
         return redirect()->back();
    }

    /**
     * Update the specified user all notifications in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();
         return redirect()->back();
    }

    /**
     * Remove the specified user notifications from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        auth()->user()->notifications()->delete();
         return redirect()->back();
    }
```

### Step 9: Create notification function in register controller
Add below code in app\Http\Controllers\Auth\RegisterController.php
```bash
use Notification;
use App\Notifications\RegisterNotification;
```
add inside create function
```bash
$users = User::get();
        
$user = User::create([
    'name' => $data['name'],
    'email' => $data['email'],
    'password' => Hash::make($data['password']),
]);

$fullUrl = route('user.show',$user->id);
$url = substr($fullUrl, strlen(url(''))+1);  /* get url after base url*/

$details = [
    'name' => 'new user('.$data['name'].') is registerd today',
    'url' => $url
];
Notification::send($users,new RegisterNotification($details));

return $user;
```
### Step 10: Update notification
Add below code in app\Notifications\RegisterNotification.php
```bash
/**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $details = $details;
        $this->name = $details['name'];
        $this->url = ($details['url'])?$details['url']:'';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','mail']; /* send notification via database and mail */
        // return ['database']; /* send notification via database */
        // return ['mail']; /* send notification via mail */
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    // ->line('The introduction to the notification.')
                    ->line($this->name)
                    ->action('Notification Action', url($this->url))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $url = $this->url;
        return [
            'name' => $this->name,
            'url' => $url,
        ];
    }
```

### Step 11: Create blade file

Goto "resources\views\layouts\app.blade.php" to grab the layouts code

Goto "resources\views\notification.blade.php" to grab the notification view code

Goto "resources\views\user.blade.php" to grab the notification details code

### Step 12: Final run and check in browser
```bash
mv server.php index.php
cp public/.htaccess .
```
open in browser
```bash
http://localhost/laravel/notification
```