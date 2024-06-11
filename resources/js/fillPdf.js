const { PDFDocument } = require('pdf-lib');
const fs = require('fs');

async function fillPdf(inputPath, outputPath, data) {
    try {
        console.log(`Input PDF Path: ${inputPath}`);
        console.log(`Output PDF Path: ${outputPath}`);
        console.log(`Data: ${JSON.stringify(data)}`);

        const existingPdfBytes = fs.readFileSync(inputPath);
        const pdfDoc = await PDFDocument.load(existingPdfBytes);
        const form = pdfDoc.getForm();

        Object.keys(data).forEach((key) => {
            const field = form.getTextField(key);
            if (field) {
                field.setText(data[key]);
            }
        });

        const pdfBytes = await pdfDoc.save();
        fs.writeFileSync(outputPath, pdfBytes);
        console.log('PDF saved successfully');
    } catch (error) {
        console.error('Error filling PDF:', error);
    }
}

try {
    const data = JSON.parse(process.argv[4]);
    const inputPath = process.argv[2];
    const outputPath = process.argv[3];

    fillPdf(inputPath, outputPath, data).then(() => {
        console.log('PDF filled successfully');
    }).catch((err) => {
        console.error('Error filling PDF:', err);
    });
} catch (error) {
    console.error('Error parsing JSON data:', error);
}
