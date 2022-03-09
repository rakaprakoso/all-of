@if ($type==1)
<h3>Halo, {{ $data->name }} !</h3>
<p>Terima kasih telah menghubungi kami, pesan anda akan dibalas secepatnya</p>
@else
<h3>Ada pesan baru!</h3>
<p>Dari : {{ $data->name }} - {{ $data->email }}</p>
<p>Subject : {{ $data->subject }}</p>
<p>Message :</p>
    <p>{{ $data->message }}</p>
@endif
