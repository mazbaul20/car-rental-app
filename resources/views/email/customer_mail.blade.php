<!DOCTYPE html>
<html>

<head>
    <title></title>
</head>

<body>
    <h3>Car Rental Confirmation of ID #{{ mail_data['id'] }}</h3>
    <p>Dear {{ mail_data['name'] }},</p>
    <p>Thank you for renting a car with us. Here are the details of your rental:</p>
    <ul>
        <li><strong>Car Name:</strong> {{ mail_data['car_name'] }}</li>
        <li><strong>Car brand:</strong> {{ mail_data['car_brand'] }}</li>
        <li><strong>Car Model:</strong> {{ mail_data['car_model'] }}</li>
        <li><strong>Car Type:</strong> {{ mail_data['car_type'] }}</li>
        <li><strong>Car YOM:</strong> {{ mail_data['car_year'] }}</li>
        <li><strong>Rental Start Date:</strong> {{ mail_data['start_date'] }}</li>
        <li><strong>Rental End Date:</strong> {{ mail_data['end_date'] }}</li>
        <li><strong>Total Cost:</strong> ${{ mail_data['total_cost'] }}</li>
    </ul>
    <p>If you have any questions, feel free to contact us.</p>
    <p>Best regards,</p>
    <p>Laravel Car Rental APP &#128512;</p>
</body>

</html>
