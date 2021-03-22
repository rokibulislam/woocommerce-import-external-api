<?php 
namespace WCMystore;

use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;

class AWSSQS {

	private $client;
	private	$queueUrl = "https://sqs.us-east-2.amazonaws.com/320176893238/mystore2";


	public function __construct() {

		$this->client = new SqsClient([
			'credentials' 	=> array (
				'key' 			=> 'AKIAUVDAHVU3NTHI56UV', 
				'secret' 		=> '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'
			),
		    'profile' => 'default',
		    'region' => 'us-east-2',
		    'version' => 'latest'
		]);

		// $this->send();
	}

	public function list() {

		error_log('fetching aws sqs list');

		try {
		    $result = $this->client->listQueues();
		    foreach ( $result->get('QueueUrls') as $queueUrl) {
		        // echo "$queueUrl\n";
		        error_log(print_r($queueUrl,true));
		    }
		} catch (AwsException $e) {
		    // output error message if fails
		    error_log($e->getMessage());
		}
	}


	public function send( $data ) {

		error_log('send product aws');

		$queue_message = json_encode( $data ); 

		$response = $this->client->sendMessage(array(
			'QueueUrl' => $this->queueUrl,  
			'MessageBody' => $queue_message
		));

		error_log(print_r($response,true));

		// $params = [
		//     'DelaySeconds' => 10,
		//     'MessageAttributes' => [
		//         "Title" => [
		//             'DataType' => "String",
		//             'StringValue' => "The Hitchhiker's Guide to the Galaxy"
		//         ],
		//         "Author" => [
		//             'DataType' => "String",
		//             'StringValue' => "Douglas Adams."
		//         ],
		//         "WeeksOn" => [
		//             'DataType' => "Number",
		//             'StringValue' => "6"
		//         ]
		//     ],
		//     'MessageBody' => "Information about current NY Times fiction bestseller for week of 12/11/2016.",
		//     'QueueUrl' => 'QUEUE_URL'
		// ];
	
		// try {
		//     // $result = $this->client->sendMessage($params);
		//     var_dump($result);
		// } catch (AwsException $e) {
		//     // output error message if fails
		//     error_log($e->getMessage());
		// }
	}


	public function receive() {

		//AWSAccessKeyId=AKIAUJ5DIG3JTVQRR4KH
		//AWSSecretKey=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

		try {
		    $result = $client->receiveMessage(array(
		        'AttributeNames' => ['SentTimestamp'],
		        'MaxNumberOfMessages' => 1,
		        'MessageAttributeNames' => ['All'],
		        'QueueUrl' => $this->queueUrl, // REQUIRED
		        'WaitTimeSeconds' => 0,
		    ));
		    if (count($result->get('Messages')) > 0) {
		        var_dump($result->get('Messages')[0]);
		        $result = $client->deleteMessage([
		            'QueueUrl' => $queueUrl, // REQUIRED
		            'ReceiptHandle' => $result->get('Messages')[0]['ReceiptHandle'] // REQUIRED
		        ]);
		    } else {
		        echo "No messages in queue. \n";
		    }
		} catch (AwsException $e) {
		    // output error message if fails
		    error_log($e->getMessage());
		}
	}
}