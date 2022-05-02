<?php
try {
    $request = new SoapClient("https://my-magento.com/soap/?wsdl&services=integrationAdminTokenServiceV1", array("soap_version" => SOAP_1_2));
} catch (SoapFault $e) {
}

$token = $request->integrationAdminTokenServiceV1CreateAdminAccessToken(array("username" => "admin", "password" => "davidova777"));

try {
    $request = new SoapClient(
        'https://my-magento.com/soap/default?wsdl&services=customerCustomerRepositoryV1',
        array(
            'soap_version' => SOAP_1_2,
            'stream_context' => stream_context_create(array(
                'http' => array('header' => 'Authorization: Bearer ' . $token->result)
            ))
        )
    );
} catch (SoapFault $e) {
}

$response = $request->customerCustomerRepositoryV1GetById(array("customerId" => 3));
echo "<pre>";
print_r($response);
echo "</pre>";

