<?php

if (! function_exists('status')) {
    function status($currentStatus='') {
    	switch($currentStatus)
		{
			// case 'Request Submitted':
       		// 	$statuses = array("Processing"=>"Processing", "Action Initiated"=>"Action Initiated", "Completed"=>"Completed", "Rejected"=>"Rejected");
       		// 	return $statuses;
       		// break;

			case 'Request Submitted':
				$statuses = array("Action Initiated"=>"Action Initiated", "Completed"=>"Completed", "Rejected"=>"Rejected");
       			return $statuses;
       		break;

       		case 'Documents Submitted':
				$statuses = array("Action Initiated"=>"Action Initiated", "Completed"=>"Completed", "Rejected"=>"Rejected");
       			return $statuses;
       		break;

       		case 'Action Initiated':
				$statuses = array("Completed"=>"Completed", "Rejected"=>"Rejected");
       			return $statuses;
       		break;

       	}
    }
}

if (! function_exists('uniqueSlugGenerator')) {
    function uniqueSlugGenerator($convertedText,$modelObject,$id=NULL) {
	    $slug = $convertedText;

    	if($id == null){
    		$data = $modelObject::withTrashed()->where('slug', 'like', $convertedText.'%')->get();
    		if($data->count() > 0){
				$slugIDs = array();
				foreach($data as $slugData) {
					$slugSplitted = explode($convertedText.'-', $slugData->slug);
					if(count($slugSplitted) > 1) {
						if(intval($slugSplitted[1]) > 0) {
							if(!in_array(intval($slugSplitted[1]), $slugIDs)) {
								$slugIDs[] = intval($slugSplitted[1]);
							}
						}
					}
				}
				$incrementNumber = 0;
				if(count($slugIDs) > 0) {
					rsort($slugIDs);
					$incrementNumber = $slugIDs[0];
				}
	    		$slug = $convertedText.'-'.($incrementNumber+1);
	    	}
    	} else {
	    	$data = $modelObject::withTrashed()->where('id','!=',$id)->where('slug', 'like', $convertedText.'%')->get();
	    	if($data->count() > 0){
	    		$slugIDs = array();
				foreach($data as $slugData) {
					$slugSplitted = explode($convertedText.'-', $slugData->slug);
					if(count($slugSplitted) > 1) {
						if(intval($slugSplitted[1]) > 0) {
							if(!in_array(intval($slugSplitted[1]), $slugIDs)) {
								$slugIDs[] = intval($slugSplitted[1]);
							}
						}
					}
				}
				$incrementNumber = 0;
				if(count($slugIDs) > 0) {
					rsort($slugIDs);
					$incrementNumber = $slugIDs[0];
				}
	    		$slug = $convertedText.'-'.($incrementNumber+1);
	    	}
    	}

       return $slug;
    }
}
if(! function_exists('getSettingsUrl')) {
	function getSettingsUrl($key) {
		$settings = new App\Models\Settings;
		$url = $settings::where('key',$key)->value('value');
		return $url;

	}
}
if (!function_exists('getAddress')) {
	function getAddress($key)
	{
		$settings = new App\Models\Settings;
		$url = $settings::where('key', $key)->value('value');
		return $url;


	}
}
// latest 5 galleries for hompage
if (! function_exists('getGalleries')) {
    function getGalleries() {

		$gallery = new App\Models\Gallery;
		$data = $gallery::where('status',1)->orderBy('date','DESC')->limit(5)->get();
		return $data;
    }
}

if(! function_exists('getLatestNews')) {
	function getLatestNews($count)
	{
		$news = new App\Models\News;
		$data = $news::latest()->limit($count)->get();
		return $data;
	}
}
if (!function_exists('getLatestCircular')) {
	function getLatestCircular()
	{
		if (Auth::guest()){
			$circulars = new App\Models\Circular;
			$data = $circulars::where('status',1)->where('region_id',1)->latest()->limit(3)->get();
			return $data;
		}else{
			$user = auth()->user();

			if($user->can('circular-list-by-region')){
				$circulars = new App\Models\Circular;
				$data = $circulars::where('status',1)->where('region_id',$user->current_working_region_id)->orWhere('region_id',1)->latest()->limit(3)->get();
				return $data;
			} else {
				$circulars = new App\Models\Circular;
				$data = $circulars::where('status',1)->latest()->limit(3)->get();
				return $data;
			}
		}

	}
}
if (! function_exists('getbearerMembers')) {
    function getbearerMembers() {
		$users = new App\Models\User;
		$data = $users::whereIn('member_designation',['President','General Secretary'])->where('show_in_frontend',1)->where('status',1)->orderBy('member_designation','DESC')->get();
		return $data;
    }
}
if (! function_exists('generateOTP')) {
    function generateOTP() {
	   $random_no_otp = mt_rand(100000, 999999);

       return $random_no_otp;
    }
}

if (! function_exists('getWelcomeContent')) {
    function getWelcomeContent() {
        $content = App\Models\Content::find(4);
        return $content;
    }
}
if (! function_exists('getHomepageSlider')) {
    function getHomepageSlider() {
        $sliders = App\Models\HomePageSlide::orderBy('sort_order','ASC')->get();
		return $sliders;
    }
}
if (! function_exists('getMediaUrl')) {
    function getMediaUrl($type,$file,$id = '',$sub_folder1 = '',$sub_folder2 = '') {
		switch($type)
		{
			case 'membershipApplication':
				return asset('membership_application_form/'.$file);
				break;
			case 'userManual':
				return asset('user_manual/'.$file);
				break;
			case 'circular':
				return asset('uploads/circulars/'.$id.'/'.$file);
				break;
            case 'tie-up':
                return asset('uploads/tie_ups/'.$id.'/'.$file);
                break;
			case 'news':
				return asset('uploads/news/'.$id.'/'.$file);
				break;
			case 'serviceRule':
				return asset('uploads/service_rules/'.$id.'/'.$file);
				break;
			case 'amendments':
				return asset('uploads/service_rules/'.$id.'/amendments/'.$file);
				break;
			case 'avatar':
				$path =  public_path('uploads/users/profile_pic/thumb_'.$file);
				$orginalImagePath =  public_path('uploads/users/profile_pic/'.$file);
				if(Illuminate\Support\Facades\File::exists($path) && $file !=null)
				{
					return asset('uploads/users/profile_pic/thumb_'.$file);
				}
				elseif(Illuminate\Support\Facades\File::exists($orginalImagePath) && $file !=null){

					return asset('uploads/users/profile_pic/'.$file);
				}
				else
				{
					return asset('uploads/users/profile_pic/avatar/avatar.png');
				}
				break;
			case 'contentImage':
				return asset('uploads/content_images/cover_image/'.$file);
				break;
			case 'sliderImage':
				return asset('uploads/content_images/'.$file);
				break;
			case 'disciplinary':
				return asset('uploads/disciplinaries/'.$id.'/'.$sub_folder1.'/'.$sub_folder2.'/'.$file);
				break;
			case 'reliefFund':
				return asset('uploads/relief_funds/'.$id.'/'.$file);
				break;
			case 'galleryFrontend':
				if($file)
				{
					return asset('uploads/gallery_images/cover_image/'.$file);
				}
				else
				{
					return asset('uploads/gallery_images/cover_image/default-cover.jpg');
				}
				break;
			case 'gallery':
					return asset('uploads/gallery_images/cover_image/'.$file);
				break;
			case 'galleryImage':
				return asset('uploads/gallery/'.$id.'/'.$file);
				break;
			case 'history':
				return asset('uploads/histories/'.$file);
				break;
			case 'homePageSlide':
				return asset('uploads/home_page_slide/'.$file);
				break;
			case 'obituaryImage':
				if($file)
				{
					return asset('uploads/obituray_images/'.$file);
				}
				else
				{
					return asset('images/obituary/obituary-default.png');
				}
				break;
			case 'scholarships':
				return asset('uploads/scholarships/'.$id.'/'.$sub_folder1.'/'.$sub_folder2.'/'.$file);
				break;
			case 'taRequests':
				return asset('uploads/ta_requests/'.$id.'/'.$sub_folder1.'/'.$sub_folder2.'/'.$file);
				break;
			case 'transferRequest':
				return asset('uploads/transfer_requests/'.$id.'/'.$sub_folder1.'/'.$sub_folder2.'/'.$file);
				break;
			case 'sampledata':
				return asset('sampledata/'.$file);
				break;
			case 'headerImage':
				return asset('images/header/'.$file);
				break;
		}
    }
}

if (! function_exists('bloodGroup')) {
    function bloodGroup() {
       	$bloodGroups = array("Any"=>"Any","A+ve"=>"A+ve", "A-ve"=>"A-ve", "B+ve"=>"B+ve", "B-ve"=>"B-ve","AB+ve"=>"AB+ve","AB-ve"=>"AB-ve","O+ve"=>"O+ve","O-ve"=>"O-ve");
       	return $bloodGroups;
    }
}

if (! function_exists('sendNotificationEmail')) {
    function sendNotificationEmail($data,$email_page) {
       	Mail::send($email_page, compact('data'), function($message) use ($data) {
            $message->to($data['email'],$data['name'])
            ->subject($data['subject']);
            //$message->from($data['from_email'], $data['from_name']);
        });

    }
}

if (! function_exists('getNotificationIcon')) {
    function getNotificationIcon($data) {
		$notificationIcons = array(
			'Circular' => asset('images/notification/svg/circulars.svg'),
			'Meeting' => asset('images/notification/svg/meeting.svg'),
			'News' => asset('images/notification/svg/news.svg'),
			'Blood Donation' => asset('images/notification/svg/blood-donation.svg'),
			'Obituary' => asset('images/notification/svg/obituaries.svg'),
			'Bulk Message Notification' => asset('images/notification/svg/messages.svg'),
			'Poll' => asset('images/notification/svg/poll.svg'),
			'Service Rules' => asset('images/notification/svg/service-rule.svg'),
			'Disciplinary' => asset('images/notification/svg/disciplinary.svg'),
			'DisciplinaryAction' => asset('images/notification/svg/disciplinary.svg'),
			'DisciplinaryIncoming' => asset('images/notification/svg/disciplinary.svg'),

			//'gallery' => asset('images/notification/svg/gallery.svg'),

		);
		return $notificationIcons[$data];

    }
}

if(! function_exists('getLatestNotifications')) {
	function getLatestNotifications($userId)
	{
		$userModel = new App\Models\User;
		$user = $userModel->find($userId);

		if($user->member_designation =='Regional Secretary' || $user->member_designation =='Member' ){
			$data['notifications'] = $user->notification()->where('archived',0)->where('read_status',0)->where('notification_status',1)->where('region_id',$user['current_working_region_id'])->latest()->limit(5)->get();
			$data['count'] = $user->notification()->where('archived',0)->where('read_status',0)->where('notification_status',1)->where('region_id',$user['current_working_region_id'])->count();
        } else {
			$data['notifications'] = $user->notification()->where('archived',0)->where('read_status',0)->where('notification_status',1)->latest()->limit(5)->get();
			$data['count'] = $user->notification()->where('archived',0)->where('read_status',0)->where('notification_status',1)->count();
        }
		return $data;
	}
}
if(! function_exists('getNotificationDescription')) {
	function getNotificationDescription($typeId,$type)
	{
		switch($type)
		{
			case 'Circular':
				$model = new App\Models\Circular;
				break;
			case 'Meeting':
				$model = new App\Models\Meeting;
				break;
			case 'News':
				$model = new App\Models\News;
				break;
			case 'Blood Donation':
				$model = new App\Models\BloodDonation;
				break;
			case 'Obituary':
				$model = new App\Models\Obituary;
				break;
			case 'Bulk Message Notification':
				$model = new App\Models\BulkMessage;
				break;
			case 'Poll':
				$model = new App\Models\Poll;
				break;
			case 'Service Rules':
				$model = new App\Models\ServiceRule;
				break;
			case 'Gallery':
				$model = new App\Models\Gallery;
				break;
            case 'Disciplinary':
                $model = new App\Models\DisciplinaryAction;
                break;
            case 'DisciplinaryAction':
                $model = new App\Models\DisciplinaryAction;
                break;
            case 'DisciplinaryIncoming':
                $model = new App\Models\DisciplinaryAction;
                break;
		}
		if($type != 'Poll')
		{
			// file_put_contents('data.php', $typeId);
			$dataModel = $model->find($typeId);
			if($dataModel != null)
			{
				$descrption = $dataModel->description;
				$excerpt = \Illuminate\Support\Str::limit(strip_tags($descrption),200, '...');
			}
			else{
				$excerpt = null;
			}
			return $excerpt;
		}
	}
}


if (! function_exists('memberDesignation')) {
    function memberDesignation($id = NULL) {
			$associationDesignations = new App\Models\AssociationDesignation();
		if($id != NULL){
			$user = App\Models\User::find($id);
			if($user->status == 1){
				// $memberDesignations = array("President"=>"President", "General Secretary"=>"General Secretary", "Treasurer"=>"Treasurer", "Vice President"=>"Vice President", "Joint Secretary"=>"Joint Secretary","Regional Secretary" =>"Regional Secretary", "Regional Secretary Incharge" => "Regional Secretary Incharge", "EC Member" => "EC Member", "EC Special Invitee" => "EC Special Invitee", "Joint Treasurer"=>"Joint Treasurer", "Member"=>"Member");
				$memberDesignations = $associationDesignations::where('is_active',1)->get();

			}else{
				// $memberDesignations = array("President"=>"President", "General Secretary"=>"General Secretary", "Treasurer"=>"Treasurer", "Vice President"=>"Vice President", "Joint Secretary"=>"Joint Secretary","Regional Secretary" =>"Regional Secretary", "Regional Secretary Incharge" => "Regional Secretary Incharge", "EC Member" => "EC Member", "EC Special Invitee" => "EC Special Invitee", "Joint Treasurer"=>"Joint Treasurer", "Member"=>"Member", "Retirement"=>"Retirement", "Resignation"=>"Resignation", "VRS"=>"VRS", "Suspended"=>"Suspended", "Dismissed"=>"Dismissed");
				$memberDesignations = $associationDesignations::all();

			}
		}
		else{
			// $memberDesignations = array("President"=>"President", "General Secretary"=>"General Secretary", "Treasurer"=>"Treasurer", "Vice President"=>"Vice President", "Joint Secretary"=>"Joint Secretary","Regional Secretary" =>"Regional Secretary", "Regional Secretary Incharge" => "Regional Secretary Incharge", "EC Member" => "EC Member", "EC Special Invitee" => "EC Special Invitee", "Joint Treasurer"=>"Joint Treasurer", "Member"=>"Member");
			$memberDesignations = $associationDesignations::where('is_active',1)->get();
		}

       return $memberDesignations;
    }
}

if (! function_exists('deleteNotification')) {
    function deleteNotification($id,$type) {
		$notification = new App\Models\Notification();
		$notification = $notification::where('type',$type)->where('type_id',$id)->first();
        if($notification != null) {
            $notification->notification_status = 0;
            $notification->archived = 1;
			if($type == 'Poll') {
				$notification->redirection_url = '/past-polls';
			}
            $notification->update();

            $notificationUser = new App\Models\NotificationUser();
			$notificationUsers = $notificationUser::where('notification_id',$notification->id)->get();
			foreach($notificationUsers as $notificationUser) {
            	$notificationUser->delete();
            }
            $notification->forceDelete();

           /* $users = App\Models\User::get();
            if($users->count() > 0) {
                $userData = array();
                foreach($users as $user) {
                    $userData[$user->id]['read_status'] = 0;
                }
                $notification->user()->sync($userData);
            }*/
        }
    }
}

if (! function_exists('gender')) {
    function gender() {
       	$genders = array("Male"=>"Male", "Female"=>"Female", "Other"=>"Other");
       	return $genders;
    }
}

if (! function_exists('scale')) {
    function scale() {
       	$scales = array("Scale I"=>"Scale I", "Scale II"=>"Scale II", "Scale III"=>"Scale III", "Scale IV"=>"Scale IV");
       	return $scales;
    }
}

if (! function_exists('getStates')) {
    function getStates() {
       	$states = [ "Andhra Pradesh",
		   "Andaman and Nicobar Islands",
		   "Arunachal Pradesh",
		   "Assam",
		   "Bihar",
		   "Chandigarh",
		   "Chhattisgarh",
		   "Dadra and Nagar Haveli",
		   "Daman and Diu",
		   "Delhi",
		   "Goa",
		   "Gujarat",
		   "Haryana",
		   "Himachal Pradesh",
		   "Jammu and Kashmir",
		   "Jharkhand",
		   "Karnataka",
		   "Kerala",
		   "Lakshadweep",
		   "Madhya Pradesh",
		   "Maharashtra",
		   "Manipur",
		   "Meghalaya",
		   "Mizoram",
		   "Nagaland",
		   "Odisha",
		   "Puducherry",
		   "Punjab",
		   "Rajasthan",
		   "Sikkim",
		   "Tamil Nadu",
		   "Telangana",
		   "Tripura",
		   "Uttarakhand",
		   "Uttar Pradesh",
		   "West Bengal"];
       	return $states;
    }
}


if (! function_exists('sendOTP')) {
    function sendOTP($mobile,$message) {
       	$client = new Aws\Pinpoint\PinpointClient([
            'version' => config('aws.version'),
            'region'  => config('aws.region'),
            'credentials' => [
                'key'    => config('aws.credentials.key'),
                'secret' => config('aws.credentials.secret'),
            ]
        ]);

        $destinationNumber =$mobile;
        $output = [];


        $result = $client->sendMessages([
            'ApplicationId' => '9696385070d74916817c91ab4b3ae0b0',
            'MessageRequest' => [
                'Addresses' => [
                    $destinationNumber => [
                        'ChannelType' => 'SMS'
                    ],
                ],
                'MessageConfiguration' => [
                    'SMSMessage' => [
                        'Body' => $message,
                        'Keyword' => "some keyword",
                        'MessageType' => "TRANSACTIONAL",
                        // 'OriginationNumber' =>  "+12065550199",
                        'SenderId' => "MySenderID",
                    ]
                ]
            ],
        ]);

        return $result->toArray();
    }
}



if (! function_exists('pushNotification')) {
    function pushNotification($userIDs, $type, $title, $link, $icon = '') {
        $serverKey = config('pushnotification.server_key');
    	if($serverKey) {
	    	if (empty($userIDs)) {
	            return;
	        }

	    	/* $tokens = array();
	    	$deviceTokens = App\Models\FcmToken::select('device_token')->whereIn("user_id", $userIDs)->get();
	    	foreach ($deviceTokens as $deviceToken) {
	    		$tokens[] = $deviceToken->device_token;
	    	} */
	    	$deviceTokens = App\Models\FcmToken::select('device_token')->whereIn("user_id", $userIDs)->distinct()->get();
			if(count($deviceTokens) > 0) {

				foreach ($deviceTokens->chunk(800) as $userDeviceToken) {
					$tokens = array();
					foreach($userDeviceToken as $deviceToken){
						$tokens[] = $deviceToken->device_token;
					}

					// Sending Push Notifications
					$requestBody = [
						'notification' => [
							'title' => $type,
							'body'  => $title,
							'icon'  => $icon ?: '',
						],
						'data' => [
							/*'from_user_id' => $from_user_id,
							'action' => $action,*/
							'click_action' => $link ?: (env('APP_URL').$link),
						],
						'registration_ids'  => $tokens
					];

					$fields = json_encode($requestBody);

					$requestHeaders = [
						'Content-Type: application/json',
						'Authorization: key=' . $serverKey,
					];

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
					curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					$response = curl_exec($ch);
					curl_close($ch);
				}
			} else {
				return false;
			}

	    }

        return;
    }

}

if (! function_exists('PostTooLargeExceptionHandler')) {
    function PostTooLargeExceptionHandler($fileSize) {
        $postSize = str_replace('M','',ini_get('post_max_size'));
        $uploadSize = str_replace('M','',ini_get('upload_max_filesize'));
        if($postSize < $fileSize) {
            if($uploadSize < $postSize) {
                return $uploadSize*1024;
            }
            else{
                return $postSize*1024;
            }
        } else {
            if($uploadSize < $postSize) {
                return $uploadSize*1024;
            }
            else{
                return $fileSize*1024;
            }
        }
    }
}

if (! function_exists('purposeOfTA')) {
    function purposeOfTA() {
		$purposes = array("General Body Meeting"=>"General Body Meeting", "EC Meeting"=>"EC Meeting", "Regional Meeting"=>"Regional Meeting", "Other TA"=>"Other TA");
		return $purposes;
    }
}

?>
