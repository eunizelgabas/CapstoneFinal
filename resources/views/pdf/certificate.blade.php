<!DOCTYPE html>
<html>
<head>
    <title>MEDICAL CERTIFICATE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>
<body>
     <div style="">
        <img src="{{ public_path('images/mdclogo.png') }}" alt="" style="max-width: 100%; height: auto; display: inline-block; margin-right: 20px;">
        <div class="intro" style="display: inline-block; margin-left:15%;">
            <h2>Mater Dei College</h2>
            <p style="font-size: 20px">Cabulijan, Tubigon, Bohol</p>
            <p style="font-size: 20px">School Clinic</p>
            <h1>Medical Certificate</h1>
        </div>

        </div>

    <br>
    <br>

    <p>This is to certify that <u>{{$medcert->patient->firstname}} {{$medcert->patient->lastname}}</u> &nbsp; Age/Sex <u>{{$age}} /{{$medcert->patient->sex}}</u> From <u>{{$medcert->patient->address}}.</p>

    <p></u>
    </p>


    <p>Was examined and treated at Mater Dei College Clinic on {{$medcert->date}}  with the following diagnosis:  </p>
    <p> <u>{{$medcert->diagnosis}}</u></p>
    <p>And would need for medical attention for <u>{{$medcert->days}}</u> days barrying complication.</p>

    <br>

    <br>


    <br>

    <u>{{$medcert->doctor->user->firstname}} {{$medcert->doctor->user->lastname}}, MD</u><br>
    <p>School Physician</p>
    <p>Lic. no. {{$medcert->doctor->lic_no}}</p>
</body>
</html>

<style>
    * {
        font-family: Arial, Helvetica, sans-serif;

    }
    body{
        /* margin: {{ 0.5 * 72 }}pt; */
    }
    image{

    }
    .styled-line {
    border: 1px solid #000; /* Set the color and thickness of the line */
    margin: 10px 0; /* Adjust the margin for spacing */
    width: 300px
}
</style>
