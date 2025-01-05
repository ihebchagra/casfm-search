import os
import json
from PyPDF2 import PdfReader

def find_chapter_info(page_num, sections):
    for chapter in sections["chapters"]:
        next_chapter_page = float('inf')
        for next_chapter in sections["chapters"]:
            if next_chapter["page"] > chapter["page"] and next_chapter["page"] < next_chapter_page:
                next_chapter_page = next_chapter["page"]

        if chapter["page"] <= page_num + 1 and page_num + 1 < next_chapter_page:
            chapter_info = {"chapter": chapter["title"]}

            if "subchapters" in chapter:
                for subchapter in chapter["subchapters"]:
                    next_subchapter_page = float('inf')
                    for next_subchapter in chapter["subchapters"]:
                        if next_subchapter["page"] > subchapter["page"] and next_subchapter["page"] < next_subchapter_page:
                            next_subchapter_page = next_subchapter["page"]

                    if subchapter["page"] <= page_num + 1 and page_num + 1 < next_subchapter_page:
                        chapter_info["subchapter"] = subchapter["title"]

                        # Check for subsections
                        if "subsections" in subchapter:
                            for subsection in subchapter["subsections"]:
                                next_subsection_page = float('inf')
                                for next_subsection in subchapter["subsections"]:
                                    if next_subsection["page"] > subsection["page"] and next_subsection["page"] < next_subsection_page:
                                        next_subsection_page = next_subsection["page"]

                                if subsection["page"] <= page_num + 1 and page_num + 1 < next_subsection_page:
                                    chapter_info["subsection"] = subsection["title"]
                                    break
                        break

            return chapter_info
    return {"chapter": "Unknown", "subchapter": "Unknown", "subsection": "Unknown"}

def process_pdfs(filepath):
    results = []
    id_counter = 1

    try:
        with open('sections.json') as f:
            sections = json.load(f)

        with open(filepath, 'rb') as file:
            pdf = PdfReader(file)
            for page_num in range(len(pdf.pages)):
                page = pdf.pages[page_num]
                text = page.extract_text()
                cleaned_text = ' '.join(text.split())

                chapter_info = find_chapter_info(page_num, sections)

                results.append({
                    "page": page_num + 1,
                    "content": cleaned_text,
                    "id": id_counter,
                    "chapter": chapter_info.get("chapter"),
                    "subchapter": chapter_info.get("subchapter"),
                    "subsection": chapter_info.get("subsection")
                })
                id_counter += 1
        print(f"Processed: casfm.pdf")
    except Exception as e:
        print(f"Error processing casfm.pdf: {str(e)}")

    return results

def main():
    filepath = 'casfm.pdf'  # Direct file path
    output_file = 'output.js'

    results = process_pdfs(filepath)

    with open(output_file, 'w', encoding='utf-8') as f:
        f.write('const casfm_pages = ')
        json.dump(results, f, ensure_ascii=False, indent=2)

    print(f"Processing complete. Output saved to {output_file}")

if __name__ == "__main__":
    main()
