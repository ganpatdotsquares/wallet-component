<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Traits\ApiGlobalFunctions;

class HomeController extends Controller
{
    use ApiGlobalFunctions;

    public function homeLocationList(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $query = User::with(['userDetail']);

            $users = $query->get();

            // Extract unique location data and concatenate state & country
            $usersLocationData = $users->map(function ($user) {
                if ($user->userDetail && $user->userDetail->state && $user->userDetail->country) {
                    return [
                        'location' => ucfirst($user->userDetail->state) . ', ' . ucfirst($user->userDetail->country),
                        'country' => ucfirst($user->userDetail->country)
                    ];
                }
                return null;
            })
            ->filter() // Remove null values
            ->unique('location') // Ensure unique locations
            ->sortBy('country') // Sort by country name in ascending order
            ->values()
            ->pluck('location'); // Extract only location strings

            if ($usersLocationData->isNotEmpty()) {
                $result['locations'] = $usersLocationData;
                return $this->sendResponse($result, $this->messageDefault('location_list'));
            }

            return $this->sendError($this->messageDefault('location_not_found'), '', 404);
        
        } catch (\Exception $th) {
            return $this->sendError($this->messageDefault('invalid_request'), '', '200');
        }
    }


    public function getUserDetail($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->sendError('User not found', '', '404');
        }

        // Prepare the response data
        $userData = $this->getStudent($user);
        return $this->sendResponse($userData, $this->messageDefault('detail_found'));
    }

    private function getNdisHealth($user)
    {
        $averageRating = $user->receivedReviews->avg('rating');
        $userData = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'stripe_account_id' => $user->stripe_account_id ?? null,
            'stripe_payouts_enabled' => $user->stripe_payouts_enabled ?? null,
            'description' => $user->userDetail && $user->userDetail->description ? $user->userDetail->description  : '',
            'experience' => $user->userDetail && $user->userDetail->experience ? $user->userDetail->experience  : '',
            'website_url' => $user->userDetail && $user->userDetail->website_url ? $user->userDetail->website_url  : '',
            'facebook_link' => $user->userDetail && $user->userDetail->facebook_link ? $user->userDetail->facebook_link  : '',
            'twitter_link' => $user->userDetail && $user->userDetail->twitter_link ? $user->userDetail->twitter_link  : '',
            'instagram_link' => $user->userDetail && $user->userDetail->instagram_link ? $user->userDetail->instagram_link  : '',
            'linkedin_link' => $user->userDetail && $user->userDetail->linkedin_link ? $user->userDetail->linkedin_link  : '',
            'is_medicare' => $user->userDetail && $user->userDetail->is_medicare == 1 ? true : false,

            'user_status' => $user->user_status,
            'profile_image' => $user->profile_image ? asset(Storage::url($user->profile_image)) : null,
            'average_rating' => $averageRating ? round($averageRating, 2) : null,
            'review_count' => $user->receivedReviews->count(),
            'address' => $user->userDetail ? $user->userDetail->address : null,
            'country' => $user->userDetail ? $user->userDetail->country : null,
            'state' => $user->userDetail ? $user->userDetail->state : null,
            'services' => $user->userService->pluck('service.name')->toArray(),
            'portfolio_images' => $user->userPortfolio->map(function ($image) {
                return asset(Storage::url($image->image_path));
            })->toArray(),
            'received_reviews' => $user->receivedReviews->map(function ($review) {
                return [
                    'rating' => $review->rating,
                    'comment' => $review->review,
                    'sender_name' => $review->giver->name,
                    'sender_profile_image' => $review->giver->profile_image ? asset(Storage::url($review->giver->profile_image)) : null,
                    'time' => $this->timeAgo($review->created_at)
                ];
            })
        ];
        return $userData;
    }

    private function getStudent($user)
    {
        $averageRating = $user->receivedReviews->avg('rating');
        $userData = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'stripe_account_id' => $user->stripe_account_id ?? null,
            'stripe_payouts_enabled' => $user->stripe_payouts_enabled ?? null,
            'profile_image' => $user->profile_image ? asset(Storage::url($user->profile_image)) : null,
            'description' => $user->userDetail && $user->userDetail->description ? $user->userDetail->description  : '',
            'country' => $user->userDetail ? $user->userDetail->country : null,
            'state' => $user->userDetail ? $user->userDetail->state : null,
            'address' => $user->userDetail ? $user->userDetail->address : null,
            'user_status' => $user->user_status,
            'university_name' => $user->userDetail && $user->userDetail->university_name ? $user->userDetail->university_name  : '',
            'joining_date' => $user->userDetail && $user->userDetail->joining_date ? $user->userDetail->joining_date  : '',
            'registration_number' => $user->userDetail && $user->userDetail->registration_number ? $user->userDetail->registration_number  : '',
            'student_id' => $user->userDetail && $user->userDetail->student_id ? $user->userDetail->student_id  : '',
            'current_course_title' => $user->userDetail && $user->userDetail->current_course_title ? $user->userDetail->current_course_title  : '',
            'course_duration' => $user->userDetail && $user->userDetail->course_duration ? $user->userDetail->course_duration  : '',
            'website_url' => $user->userDetail && $user->userDetail->website_url ? $user->userDetail->website_url  : '',
            'facebook_link' => $user->userDetail && $user->userDetail->facebook_link ? $user->userDetail->facebook_link  : '',
            'twitter_link' => $user->userDetail && $user->userDetail->twitter_link ? $user->userDetail->twitter_link  : '',
            'instagram_link' => $user->userDetail && $user->userDetail->instagram_link ? $user->userDetail->instagram_link  : '',
            'linkedin_link' => $user->userDetail && $user->userDetail->linkedin_link ? $user->userDetail->linkedin_link  : '',
            'is_medicare' => $user->userDetail && $user->userDetail->is_medicare == 1 ? true : false,
            'accept_donation' => $user->userDetail && $user->userDetail->accept_donation ? $user->userDetail->accept_donation  : '',
            'is_public_participant' => $user->userDetail && $user->userDetail->is_public_participant ? $user->userDetail->is_public_participant  : '',
            'like_count' => $user->like_count,
            'dislike_count' => $user->dislike_count,
            'average_rating' => $averageRating ? round($averageRating, 2) : null,
            'review_count' => $user->receivedReviews->count(),
            'services' => $user->userService->pluck('service.name')->toArray(),
            'portfolio_images' => $user->userPortfolio->map(function ($image) {
                return asset(Storage::url($image->image_path));
            })->toArray(),
            'research_papers' => $user->userResearchDocument->map(function ($research) {
                return asset(Storage::url($research->certificate));
            })->toArray(),
            'received_reviews' => $user->receivedReviews->map(function ($review) {
                return [
                    'rating' => $review->rating,
                    'comment' => $review->review,
                    'sender_name' => $review->giver->name,
                    'sender_profile_image' => $review->giver->profile_image ? asset(Storage::url($review->giver->profile_image)) : null,
                    'time' => $this->timeAgo($review->created_at)
                ];
            })
        ];
        return $userData;
    }
    public function getEventDetail($id)
    {
        try {
            $event = Event::with(['images', 'reviews', 'reviews.giver'])
                ->find($id);
            if (!$event) {
                return $this->sendError('Event not found', '', '404');
            }
            $averageRating = $event->reviews->avg('rating');
            $organiser = [];
            $event_organiser = $this->getUserDetail($event->user_id)->original;
            if ($event_organiser && $event_organiser['status'] == true) {
                $organiser = $event_organiser['data'];
            }
            if ($organiser && !empty($organiser)) {
                unset($organiser['received_reviews']);
                unset($organiser['portfolio_images']);
                unset($organiser['services']);
            }
            // Map event detail data
            $eventData = [
                'id' => $event->id,
                'event_name' => $event->event_name,
                'description' => $event->description,
                'start_date' => $event->start_date,
                'start_time' => $event->start_time,
                'end_date' => $event->end_date,
                'end_time' => $event->end_time,
                'address' => $event->address,
                'website_url' => $event->website_url,
                'facebook_link' => $event->facebook_link,
                'twitter_link' => $event->twitter_link,
                'instagram_link' => $event->instagram_link,
                'linkedin_link' => $event->linkedin_link,
                'images' => $event->images->map(function ($image) {
                    return asset(Storage::url($image->image_path));
                }),
                'average_rating' => $averageRating ? round($averageRating, 2) : null,
                'review_count' => $event->reviews->count(),
                'reviews' => $event->reviews->map(function ($review) {
                    return [
                        'rating' => $review->rating,
                        'comment' => $review->review,
                        'sender_name' => $review->giver->name,
                        'sender_profile_image' => $review->giver->profile_image ? asset(Storage::url($review->giver->profile_image)) : null,
                        'created_at' => $this->timeAgo($review->created_at),
                    ];
                }),
                'organiser' => $organiser,
            ];

            // Prepare the result
            $result['event'] = $eventData;

            // Return the response
            return $this->sendResponse($result, $this->messageDefault('event_detail'));
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->sendError($this->messageDefault('event_detail_not_found'), '', '404');
        } catch (\Exception $th) {
            return $this->sendError($this->messageDefault('invalid_request'), '', '200');
        }
    }
}
