<?php

//header('Content-Disposition: attachment; filename="proposta.pdf"');
//header('Content-Type: application/pdf');
//header('Content-Length: ' . strlen($html));
//header('Connection: close');

echo $html;

?>

<script src="/_app_cadastro_cliente/inc/html2canvas.min.js?"></script>
<script src="https://unpkg.com/jspdf@latest/dist/jspdf.min.js"></script>
<script>

    function generateCanvasPerPage(index){
        $('.loading-overlay').show();
        html2canvas($('.page-break[data-page="'+index+'"] .sheet')[0]).then(canvas => {
            $(canvas).hide();
            document.body.appendChild(canvas);
            if($('.page-break[data-page="'+(index+1)+'"] .sheet')[0]) {
                generateCanvasPerPage(index+1);
            } else {
                savePdf();
            }
        });
    }
    generateCanvasPerPage(1);

    function savePdf() {
        var pdf = new jsPDF("p", "pt", "a4", true);
        var canvasEl = document.querySelectorAll("canvas");

        canvasEl.forEach(function(canvas,index){
            // canvas.getContext('2d').fillRect(0,0,100*(index+1),100*(index+1));
            pdf.addImage(canvas.toDataURL("image/png", 1), 'JPEG', 0, 0, (canvas.width-260), 850,undefined,'FAST');
            if(index == canvasEl.length-1){
                var download = pdf.save("file.pdf", {returnPromise: true}).then(function() {
                    window.close()
                });
            }
            else {
                pdf.addPage();
            }
        })
    }
</script>