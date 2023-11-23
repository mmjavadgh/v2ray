<?php
// تنظیمات
$telegramBotToken = '6520725138:AAF9YpmlJ0ypzFyZwaNr0hf_60xh9RW_kFc';
$channelUsername = 'TVCminer';
$githubUsername = 'mmjavadgh';
$githubRepo = 'v2ray';
$githubToken = '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';

// تابع برای دریافت لینک‌ها از پست‌های کانال تلگرام
function getV2rayLinks($channelUsername, $telegramBotToken) {
    $apiUrl = "https://api.telegram.org/bot{$telegramBotToken}/getChat?chat_id={$channelUsername}";
    $response = file_get_contents($apiUrl);
    $data = json_decode($response, true);

    $links = [];

    if ($data && isset($data['result']['posts'])) {
        foreach ($data['result']['posts'] as $post) {
            // اینجا باید الگوی مربوط به لینک‌های v2ray را بیابید و اضافه کنید
            $matches = [];
            preg_match_all('/YOUR_REGEX_PATTERN/', $post['text'], $matches);

            // اضافه کردن لینک‌ها به آرایه
            if (!empty($matches[0])) {
                $links = array_merge($links, $matches[0]);
            }
        }
    }

    return $links;
}

// تابع برای ذخیره لینک‌ها در یک فایل txt در گیت‌هاب
function saveLinksToGitHub($links, $githubUsername, $githubRepo, $githubToken) {
    $fileContent = implode("\n", $links);
    $apiUrl = "https://api.github.com/repos/{$githubUsername}/{$githubRepo}/contents/v2ray_links.txt";
    $data = [
        'message' => 'Update v2ray links',
        'content' => base64_encode($fileContent),
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/json\r\n" .
                "Authorization: token {$githubToken}\r\n",
            'method' => 'PUT',
            'content' => json_encode($data),
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($apiUrl, false, $context);

    return $result;
}

// اجرای توابع
$links = getV2rayLinks($channelUsername, $telegramBotToken);
$result = saveLinksToGitHub($links, $githubUsername, $githubRepo, $githubToken);

// نمایش نتیجه
if ($result) {
    echo "Links updated successfully.";
} else {
    echo "Failed to update links.";
}
?>
