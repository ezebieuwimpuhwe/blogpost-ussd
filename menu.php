<?php
include 'config.php';
include 'sms.php';

class BlogUSSD {
    private $sms;

    public function __construct() {
        $this->sms = new Sms(); // Initialize SMS class
    }

    // Main Menu
    public function mainMenu() {
        return "CON Welcome to Blog USSD\n1. Subscribe\n2. Unsubscribe\n3. Rate Blog\n4. Suggest Topic\n5. Feedback\n6. Exit";
    }

    // Handle User Input Based on the Menu Selection
    public function handleUserInput($text, $phoneNumber) {
        global $conn;
        $input = explode("*", $text);
        $level = count($input);

        switch ($input[0]) {
            case "1": // Subscribe
                return $this->handleSubscription($level, $input, $phoneNumber);

            case "2": // Unsubscribe
                return $this->handleUnsubscription($level, $input, $phoneNumber);

            case "3": // Rate Blog
                return $this->handleRating($level, $input, $phoneNumber);

            case "4": // Suggest Topic
                return $this->handleTopicSuggestion($level, $input, $phoneNumber);

            case "5": // Feedback
                return $this->handleFeedback($level, $input, $phoneNumber);

            default:
                return $this->mainMenu();
        }
    }

    // Handle Subscription
    private function handleSubscription($level, $input, $phoneNumber) {
        global $conn;

        if ($level == 1) {
            return "CON Confirm subscription?\n1. Yes\n2. No";
        } elseif ($level == 2 && $input[1] == "1") {
            $stmt = $conn->prepare("INSERT IGNORE INTO subscribers (phone_number) VALUES (?)");
            $stmt->execute([$phoneNumber]);

            // Send SMS to confirm subscription
            $message = "Thank you for subscribing to Blog USSD updates.";
            $this->sms->sendSMS($message, $phoneNumber);

            return "END You are now subscribed!";
        } else {
            return "END Subscription cancelled.";
        }
    }

    // Handle Unsubscription
    private function handleUnsubscription($level, $input, $phoneNumber) {
        global $conn;

        if ($level == 1) {
            return "CON Confirm unsubscribe?\n1. Yes\n2. No";
        } elseif ($level == 2 && $input[1] == "1") {
            $stmt = $conn->prepare("UPDATE subscribers SET status = 'inactive' WHERE phone_number = ?");
            $stmt->execute([$phoneNumber]);

            // Send SMS to confirm unsubscription
            $message = "You have been unsubscribed from Blog USSD updates.";
            $this->sms->sendSMS($message, $phoneNumber);

            return "END You are unsubscribed.";
        } else {
            return "END Unsubscribe cancelled.";
        }
    }

    // Handle Rating
    private function handleRating($level, $input, $phoneNumber) {
        global $conn;

        if ($level == 1) {
            return "CON Rate blog (1-5):";
        } elseif ($level == 2) {
            $rating = intval($input[1]);
            if ($rating >= 1 && $rating <= 5) {
                $stmt = $conn->prepare("INSERT INTO ratings (phone_number, rating) VALUES (?, ?)");
                $stmt->execute([$phoneNumber, $rating]);

                // Send SMS after rating
                $message = "Thank you for rating the Blog! Your rating: $rating.";
                $this->sms->sendSMS($message, $phoneNumber);

                return "END Thank you for rating!";
            } else {
                return "END Invalid rating.";
            }
        }
    }

    // Handle Topic Suggestion
    private function handleTopicSuggestion($level, $input, $phoneNumber) {
        global $conn;

        if ($level == 1) {
            return "CON Enter your topic suggestion:";
        } elseif ($level == 2) {
            $stmt = $conn->prepare("INSERT INTO topics (phone_number, suggestion) VALUES (?, ?)");
            $stmt->execute([$phoneNumber, $input[1]]);

            // Send SMS after topic suggestion
            $message = "Thank you for your suggestion!";
            $this->sms->sendSMS($message, $phoneNumber);

            return "END Thanks for your suggestion!";
        }
    }

    // Handle Feedback
    private function handleFeedback($level, $input, $phoneNumber) {
        global $conn;

        if ($level == 1) {
            return "CON Feedback Type:\n1. Comment\n2. Issue";
        } elseif ($level == 2) {
            return "CON Enter your message:";
        } elseif ($level == 3) {
            $type = $input[1] == "1" ? "comment" : "issue";
            $message = $input[2];
            $stmt = $conn->prepare("INSERT INTO feedback (phone_number, feedback_type, message) VALUES (?, ?, ?)");
            $stmt->execute([$phoneNumber, $type, $message]);

            // Send SMS after feedback submission
            $message = "Thank you for your feedback!";
            $this->sms->sendSMS($message, $phoneNumber);

            return "END Thank you for your feedback!";
        }
    }
}
?>
