<html>
    <head>
        <title>Patient List</title>

    </head>
    <style>
        *{
            font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            font-size: 11pt;
        }
        h1{
            font-size: 22pt;

        }

        table{
            border-collapse: collapse;
        }
        table th, table td{
            padding:2px;
            border: 1px solid #333;
        }

    </style>

    <body>
        <div style="text-align: center; padding-bottom: 12pt; border-bottom: 2px solid #333">

            <img src="{{ public_path('images/mdclogo.png') }}" style="height: 70px; border-radius: 50px; float: left; margin-right: 10px;" alt="RQR Piggery Farm, Inc.">
            <div style="text-align: left;">
                <strong>Mater Dei College</strong> <br>
                Cabulijan, Tubigon, Bohol <br>
                School Clinic<br>
            </div>

        </div>


        <h2 style="padding-bottom: 10pt; border-bottom: 1px solid #333; margin-top:10">Doctor Lists</h2>

        <table style=" width: 100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>Gender</th>
                    <th>Medical Service</th>

                </tr>
            </thead>

            <tbody>

                    @foreach ($doctor as $item )
                    <tr>
                        <td style="text-align: center">
                            {{$item->user->firstname}} {{$item->user->lastname}}
                        </td>
                        <td style="text-align: center;">
                            {{$item->specialization}}
                        </td>
                        <td style="text-align: center;">
                            {{$item->user->gender}}
                        </td>
                        <td style="text-align: center;">
                            @foreach ($item->services as $service)
                            {{ $service->name }} <br>
                            @endforeach
                        </td>

                    </tr>

                    @endforeach

            </tbody>

        </table>

    </body>
</html>
