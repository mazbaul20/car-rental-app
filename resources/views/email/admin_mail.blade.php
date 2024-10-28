<!DOCTYPE html>
<html>

<head>
    <title></title>
</head>

<body>
    <h3>Car Rental Notification of ID #{{ mail_data['id'] }}</h3>
    <p>Dear Admin,</p>
    <p>A car has been rented by the following customer:</p>
    <ul>
        <li><strong>Customer Name:</strong> {{ mail_data['name'] }}</li>
        <li><strong>Customer Email:</strong> {{ mail_data['email'] }}</li>
        <li><strong>Customer Phone:</strong> {{ mail_data['phone'] }}</li>
    </ul>
    <p>Here are the details of the rental:</p>
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
</body>

</html>
