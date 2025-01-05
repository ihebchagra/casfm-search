import os
from pdf2image import convert_from_path
from PIL import Image
import io

def convert_pdf_to_webp(pdf_path, output_folder):
    if not os.path.exists(output_folder):
        os.makedirs(output_folder)

    pages = convert_from_path(pdf_path)
    for page_num, page in enumerate(pages):
        output_path = os.path.join(output_folder, f'page_{page_num + 1}.webp')
        page.save(output_path, 'WEBP')
        print(f"Saved page {page_num + 1} as {output_path}")

def main():
    pdf_path = 'casfm.pdf'
    output_folder = 'pdf_pages'

    try:
        convert_pdf_to_webp(pdf_path, output_folder)
        print("PDF conversion complete!")
    except Exception as e:
        print(f"Error converting PDF: {str(e)}")

if __name__ == "__main__":
    main()
