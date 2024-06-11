<!DOCTYPE html>
<html>

<head>
    <title>PDF Form</title>
</head>

<body onload="fillPdfForm()">
    <form id="myForm">
        <!-- Your PDF form fields go here -->
        <input type="text" id="name" name="name" value="xxxxx">
        <input type="text" id="date" name="date" value="xxxxx">
        <input type="text" id="invoice_number" name="invoice_number" value="xxxxx">
        <!-- Add more form fields as needed -->
    </form>

    <!-- Include pdfform.js -->
    <script src="https://cdn.jsdelivr.net/npm/pdfform.js/dist/pdfform.min.js"></script>

    <script>
        function fillPdfForm() {
            var formData = {
                "0": document.getElementById('name').value,
                "1": document.getElementById('date').value,
                "2": document.getElementById('invoice_number').value
                // Add more form field values as needed
            };

            // Load the PDF template asynchronously
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '{{ asset("pdf/approve_wh3_081156.pdf") }}', true);
            xhr.responseType = 'arraybuffer';

            xhr.onload = function() {
                if (xhr.status === 200) {
                    var pdfData = xhr.response;

                    // Fill the form
                    pdfform().transform(pdfData,formData, function(err, formData) {
                        if (err) return console.log(err);

                        // Download the filled PDF
                        pdfform().download();
                    });
                }
            };

            xhr.send(null);
        }
    </script>
</body>
</html>
