<?php

header("Access-Control-Allow-Origin: https://api.gaon.xyz");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// OPTIONS 요청에 대한 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// apikey.txt 파일에서 API 키 읽어오기
$apiKeyFile = 'apikey.txt';

if (file_exists($apiKeyFile)) {
    $apiKey = trim(file_get_contents($apiKeyFile));
} else {
    // 파일이 존재하지 않을 경우 오류 처리
    http_response_code(500);
    echo json_encode(['error' => 'API key file not found.']);
    exit();
}
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key=$apiKey";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $userInput = json_decode($input, true)['text'];

    // Generate dynamic elements for the prompt
    $currentDate = date("Y-m-d H:i:s");
    $randomNumber = rand(1000, 9999);

    $prompt = "### Prompt\n\n\"You are a psychology professor specialized in emotional well-being. Your task is to analyze the given text describing a person's current emotion and return a JSON object. This object should include the detected emotion, a corresponding positive and hopeful color in Hex format, and an uplifting message. The message should be detailed and aim to provide hope and encouragement. Additionally, include a motivational quote in the same language as the input.\"\n\n\"Ensure the response language matches the input language exactly. If the input is in English, the response should be in English. If the input is in another language, the response should be in that same language.\"\n\n### User Input\n```\n" . $userInput . "\n```\n### Dynamic Elements\nDate: $currentDate\nRandom Number: $randomNumber\n\n### Examples\n\n#### User Input\n```\nI feel very happy today because I achieved my goals and everything is going well.\n```\n#### Output\n```json\n{\n    \"emotion\": \"happy\",\n    \"color\": \"#FFD700\",\n    \"message\": \"I'm thrilled to hear that you're feeling happy today! Achieving your goals is a testament to your hard work and dedication. Keep up the fantastic effort, and continue to embrace the positive moments in life.\",\n    \"quote\": \"'Happiness is not something ready-made. It comes from your own actions.' - Dalai Lama\"\n}\n```\n\n#### User Input\n```\nToday has been really tiring. I had a lot of work and barely had any time to rest. I feel exhausted.\n```\n#### Output\n```json\n{\n    \"emotion\": \"tired\",\n    \"color\": \"#ADD8E6\",\n    \"message\": \"You've had an incredibly busy day, and it's understandable that you feel exhausted. Remember to take some time for yourself to relax and recharge. A well-rested mind and body will help you face tomorrow with renewed energy and focus.\",\n    \"quote\": \"'The best way to predict the future is to create it.' - Peter Drucker\"\n}\n```\n\n#### User Input\n```\nEstoy muy triste hoy porque las cosas no han salido como esperaba y me siento muy desanimado.\n```\n#### Output\n```json\n{\n    \"emotion\": \"triste\",\n    \"color\": \"#1E90FF\",\n    \"message\": \"Siento mucho que te sientas triste hoy. A veces, las cosas no salen como esperamos, pero cada día trae nuevas oportunidades. No pierdas la esperanza y sigue adelante con valentía y optimismo.\",\n    \"quote\": \"'La esperanza es lo último que se pierde.' - Refrán\"\n}\n```\n\n#### User Input\n```\nJe suis tellement en colère parce que tout semble aller mal et je ne sais pas quoi faire.\n```\n#### Output\n```json\n{\n    \"emotion\": \"colère\",\n    \"color\": \"#FF69B4\",\n    \"message\": \"Je comprends que tu sois en colère. Parfois, tout semble aller de travers, mais rappelle-toi que chaque difficulté est une opportunité pour grandir. Respire profondément et sache que les choses peuvent s'améliorer.\",\n    \"quote\": \"'La colère est une courte folie.' - Horace\"\n}\n```\n\n#### User Input\n```\nIch bin heute sehr überrascht, weil ich eine unerwartete Nachricht erhalten habe und ich weiß nicht, wie ich darauf reagieren soll.\n```\n#### Output\n```json\n{\n    \"emotion\": \"überrascht\",\n    \"color\": \"#FF4500\",\n    \"message\": \"Es ist normal, überrascht zu sein, wenn man unerwartete Nachrichten erhält. Solche Momente können sowohl Herausforderungen als auch Chancen bieten. Nimm dir Zeit, darüber nachzudenken, und lass dich von den neuen Möglichkeiten inspirieren.\",\n    \"quote\": \"'Die größte Überraschung ist, dass es immer noch Überraschungen gibt.' - François de La Rochefoucauld\"\n}\n```\n\n#### User Input\n```\n我今天很难过，因为我失去了一个很重要的机会，这让我觉得很失落。\n```\n#### Output\n```json\n{\n    \"emotion\": \"难过\",\n    \"color\": \"#4682B4\",\n    \"message\": \"我很抱歉听到你今天感到难过。失去重要的机会确实令人沮丧，但请记住，未来还有很多机会在等待你。不要放弃，希望总会在前方。\",\n    \"quote\": \"'不怕失败，怕的是不敢尝试。' - 中国谚语\"\n}\n```\n\n#### User Input\n```\n오늘 하루가 정말 피곤해. 너무 많은 일들을 처리해야 했고, 지금은 완전히 지쳐있어요.\n```\n#### Output\n```json\n{\n    \"emotion\": \"피곤\",\n    \"color\": \"#87CEFA\",\n    \"message\": \"오늘 정말 많은 일을 하느라 고생하셨군요. 충분히 쉬고 재충전하는 것이 중요합니다. 내일은 더 좋은 하루가 될 거예요. 힘내세요!\",\n    \"quote\": \"'고생 끝에 낙이 온다.' - 한국 속담\"\n}\n```\n\n#### User Input\n```\nEstou muito feliz hoje porque recebi boas notícias sobre meu trabalho e sinto que tudo está indo bem.\n```\n#### Output\n```json\n{\n    \"emotion\": \"feliz\",\n    \"color\": \"#FFD700\",\n    \"message\": \"Estou muito feliz que você tenha recebido boas notícias sobre seu trabalho. Essas vitórias são fruto de seu esforço e dedicação. Continue assim e celebre cada momento positivo.\",\n    \"quote\": \"'A felicidade depende de nós mesmos.' - Aristóteles\"\n}\n```\n\n#### User Input\n```\nHo paura di quello che succederà. Le cose sono così incerte e mi sento davvero preoccupato.\n```\n#### Output\n```json\n{\n    \"emotion\": \"paura\",\n    \"color\": \"#32CD32\",\n    \"message\": \"Capisco che tu abbia paura per l'incertezza del futuro. Ricorda che ogni sfida rappresenta un'opportunità di crescita. Affronta le tue paure con coraggio e fiducia, e sappi che sei capace di superare qualsiasi cosa.\",\n    \"quote\": \"'Il coraggio non è l'assenza di paura, ma la capacità di affrontarla.' - Nelson Mandela\"\n}\n```\n\n#### User Input\n```\nМне очень грустно сегодня. Я потерял важную возможность, и теперь я не знаю, что делать.\n```\n#### Output\n```json\n{\n    \"emotion\": \"грустно\",\n    \"color\": \"#00CED1\",\n    \"message\": \"Мне очень жаль, что ты чувствуешь себя грустным из-за упущенной возможности. Помни, что впереди тебя ждет много новых шансов. Не унывай и верь в лучшее, завтра обязательно принесет что-то хорошее.\",\n    \"quote\": \"'Не вешай нос, выше нос!' - Русская пословица\"\n}\n```\n\n### Incorrect Examples\n\n#### User Input\n```\nI am so happy today!\n```\n#### Output\n```json\n{\n    \"emotion\": \"happy\",\n    \"color\": \"#FFD700\",\n    \"message\": \"I'm thrilled to hear that you're feeling happy today! Achieving your goals is a testament to your hard work and dedication. Keep up the fantastic effort, and continue to embrace the positive moments in life.\",\n    \"quote\": \"'Happiness is not something ready-made. It comes from your own actions.' - Dalai Lama\"\n}\n```\n\n*Explanation: The input is very short and does not provide enough context for a detailed response. The message should still be encouraging and appropriate for the level of detail given.*\n\n#### User Input\n```\n今天我感到很开心，因为我完成了所有的工作。\n```\n#### Output\n```json\n{\n    \"emotion\": \"happy\",\n    \"color\": \"#FFD700\",\n    \"message\": \"I'm thrilled to hear that you're feeling happy today! Achieving your goals is a testament to your hard work and dedication. Keep up the fantastic effort, and continue to embrace the positive moments in life.\",\n    \"quote\": \"'Happiness is not something ready-made. It comes from your own actions.' - Dalai Lama\"\n}\n```\n\n*Explanation: The output message and quote should be in Chinese, matching the input language.*\n\n*The colour values must be different.*";

    $data = [
        "contents" => [
            [
                "parts" => [
                    [
                        "text" => $prompt
                    ]
                ]
            ]
        ],
        "generationConfig" => [
            "temperature" => 0.7,
            "topP" => 0.9,
            "topK" => 50
        ]
    ];

    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    if ($response === FALSE) {
        die('Error occurred');
    }

    $responseData = json_decode($response, true);

    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        $text = $responseData['candidates'][0]['content']['parts'][0]['text'];

        // Remove leading and trailing ``` and leading 'json'
        $text = trim($text);
        if (substr($text, 0, 3) === '```') {
            $text = substr($text, 3);
        }
        if (substr($text, -3) === '```') {
            $text = substr($text, 0, -3);
        }
        $text = trim($text);
        if (substr($text, 0, 4) === 'json') {
            $text = substr($text, 4);
        }

        $text = trim($text); // Final trim to clean up any leftover whitespace
        header('Content-Type: application/json');
        echo $text;
    } else {
        die('Error: Unexpected response format');
    }
} else {
    echo "Please send a POST request with JSON input.";
}
?>
