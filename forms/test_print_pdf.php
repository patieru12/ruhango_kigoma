<!DOCTYPE html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <script>
	var idPrint;
	var idPdf;
	idPdf = document.getElementById('idPdf');
	idPrint = document.getElementbyId('idPrint');
    function PrintPdf() {
        //idPrint.disabled = 0;
        idPdf.Print();
    }

    function idPdf_onreadystatechange() {
        if (idPdf.readyState === 4)
            setTimeout(PrintPdf(), 1000);
    }
    </script>

</head>
<a href='./files/20151205A1.pdf'>Print Me</a>
<body>
    <button id="idPrint" onclick="PrintPdf()">Print</button>
    <br>
    <object id="idPdf" onreadystatechange="idPdf_onreadystatechange()"
        width="300" height="400" type="application/pdf"
        data="./files/20151205A1.pdf?#view=Fit&scrollbar=0&toolbar=0&navpanes=0">
        <span>PDF plugin is not available.</span>
    </object>
</body>