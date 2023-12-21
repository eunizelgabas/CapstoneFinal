<h4>
    MDC Clinic <br>
    Tubigon, Bohol
</h4>

<h1>Appointment Confirmation</h1>

<p>
    Dear  {{$patient->firstname}}!
</p>
<p>We are pleased to inform you that your appointment at Mater Dei College Clinic has been successfully booked.</p>

<p><strong>Appointment Details:</strong></p>
<ul>
    <li><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->date)->format('F d, Y') }}</li>
    <li><strong>Time:</strong>  {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</li>
    <li><strong>Doctor:</strong> {{ $doctor->user->firstname }} {{ $doctor->user->lastname }}</li>
    <!-- Add any other relevant appointment details here -->
</ul>
<p>If you have any questions or need to make changes to your appointment, please contact us at mdcclinic@gmail.com</p>

    <p>Your health and well-being are important to us, and we are committed to delivering the highest level of care.</p>
    <p>Best regards,<br>
       MDC Clinic</p>
<div class="footer">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td class="content-block">
          <span class="apple-link">Mater Dei College </span>
          <br> Clinic Management System <span>V.1</span>.
        </td>
      </tr>

    </table>
  </div>

