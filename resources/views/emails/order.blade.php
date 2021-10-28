<!doctype html>
<html lang="en">
<head>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test Mail</title>
</head>
<body>
<div class="container">
    <h1>{{$details['title']}}</h1>
    <div class="col-8">
        <table>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Amount</th>
            </tr>
            <tr>
                <td>{{$details['product']['name']}}</td>
                <td>€{{$details['product']['price']}}</td>
                <td>{{$details['stock']}}</td>
                <td>Total : €{{$details['total']}}</td>
            </tr>
        </table>
    </div>
    <div class="row">
        <div class="col-sm">
            <h2>User Information</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-sm">
            Email : {{$details['user']['email']}}
        </div>
    </div>
    <div class="row">
        <div class="col-sm">
            Address : {{$details['user']['address']}}
        </div>
    </div>
    <div class="row">
        <div class="col-sm">
            Postal : {{$details['user']['postal']}}
        </div>
    </div>
</div>
</body>
</html>