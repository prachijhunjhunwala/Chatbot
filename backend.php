<?php
header("Content-Type: application/json; charset=UTF-8");
include "db_connect.php";

$message = isset($_GET['message']) ? trim($_GET['message']) : "";
$reply = "";

// 1️⃣ Check SQL dataset first
if ($message !== "") {
    $stmt = $conn->prepare("SELECT answer FROM faq_data WHERE question LIKE ? LIMIT 1");
    $likeMessage = "%" . $message . "%";
    $stmt->bind_param("s", $likeMessage);
    $stmt->execute();
    $stmt->bind_result($answer);
    if ($stmt->fetch()) {
        $reply = $answer;  // Found in SQL
    }
    $stmt->close();
}

// 2️⃣ If no SQL match, call Gemini API
if ($reply == "") {
    $gemini_api_key = "AIzaSyDl56_Tujpko6GQ8f3SOJvCCWu_7cXpzto";
    if (!$gemini_api_key) {
        $reply = "❌ API key not configured";
    } else {
        // ✅ Using gemini-2.5-flash
        $url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=" . urlencode($gemini_api_key);
        
        $prompt = "Answer this question about BCCL Dhanbad: " . $message;
        
        // ✅ Simplified payload
        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $reply = "❌ Connection error: " . curl_error($ch);
        } elseif ($http_code !== 200) {
            $reply = "❌ API error (HTTP $http_code): Check your API key or quota";
        } else {
            $json = json_decode($result, true);
            
            // ✅ CORRECT response structure
            if (isset($json["candidates"][0]["content"]["parts"][0]["text"])) {
                $reply = trim($json["candidates"][0]["content"]["parts"][0]["text"]);
            } elseif (isset($json["candidates"][0]["content"]["parts"])) {
                // Handle case where parts array exists but might be empty
                $reply = "⚠️ Model returned empty response. Try rephrasing your question.";
            } else {
                // Debug: show what we got
                $reply = "⚠️ API response issue. Response: " . json_encode($json["candidates"][0] ?? []);
            }
        }
        curl_close($ch);
    }
}

// 3️⃣ Save chat history
if ($message !== "" && $reply !== "") {
    $stmt = $conn->prepare("INSERT INTO chat_history (user_message, bot_reply) VALUES (?, ?)");
    $stmt->bind_param("ss", $message, $reply);
    $stmt->execute();
    $stmt->close();
}

// 4️⃣ Return JSON
echo json_encode(["reply" => $reply], JSON_UNESCAPED_UNICODE);
$conn->close();
?>
