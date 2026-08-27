import puppeteer from 'puppeteer';
import { fileURLToPath } from 'url';
import path from 'path';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const htmlPath = path.join(__dirname, 'MATERIALS_SEQUENCE_DIAGRAM.html');
const pdfPath = path.join(__dirname, 'MATERIALS_SEQUENCE_DIAGRAM.pdf');
const fileUrl = 'file:///' + htmlPath.replace(/\\/g, '/');

const browser = await puppeteer.launch({
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
});

try {
    const page = await browser.newPage();
    await page.goto(fileUrl, { waitUntil: 'networkidle0', timeout: 120000 });

    await page.waitForFunction(
        () => {
            const blocks = document.querySelectorAll('.mermaid');
            if (blocks.length < 7) return false;
            return [...blocks].every((b) => b.querySelector('svg'));
        },
        { timeout: 120000 }
    );

    await page.pdf({
        path: pdfPath,
        format: 'A4',
        printBackground: true,
        margin: { top: '12mm', right: '10mm', bottom: '12mm', left: '10mm' },
    });

    console.log('PDF created:', pdfPath);
} finally {
    await browser.close();
}
