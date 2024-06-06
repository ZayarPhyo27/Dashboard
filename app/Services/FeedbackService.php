<?php

namespace App\Services;

use App\Models\Feedback;

class FeedbackService{

    public function saveCustomerFeedback( $request ){

        return Feedback::create($request);
    }
}

?>