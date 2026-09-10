"""
Express AutoFill - กรอกฟอร์ม 6 ช่องอัตโนมัติ
ช่อง: เลขเอกสาร | วันที่ | รหัสลูกค้า | อ้างอิง | จำนวน | ราคา

วิธีใช้:
1. pip install openpyxl pyautogui
2. เตรียมไฟล์ Excel ชื่อ data.xlsx ไว้โฟลเดอร์เดียวกับสคริปต์นี้
   คอลัมน์ A=เลขเอกสาร B=วันที่ C=รหัสลูกค้า D=อ้างอิง E=จำนวน F=ราคา (แถว 1 = หัวตาราง)
3. เปิดโปรแกรม Express วางเคอร์เซอร์ที่ช่อง "เลขเอกสาร" บรรทัดแรก
4. รัน: python express_filler.py -> มีเวลา 5 วินาทีให้สลับไปโปรแกรม Express
5. มันจะพิมพ์ Tab เพื่อไปช่องถัดไป, พอครบ 6 ช่องจะกด Enter เพื่อขึ้นเอกสารใหม่

ถ้าโปรแกรมของคุณใช้ปุ่มอื่นเพื่อไปช่องถัดไปหรือบันทึก ให้แก้ตัวแปรด้านล่าง
"""
import time
import sys

# --- ตั้งค่าตรงนี้ถ้าปุ่มไม่ตรงกับโปรแกรมคุณ ---
DELAY_BETWEEN_FIELDS = 0.15  # วินาทีระหว่างแต่ละช่อง
DELAY_BETWEEN_ROWS = 0.5     # วินาทีระหว่างแต่ละเอกสาร
KEY_BETWEEN_FIELDS = "tab"   # ปุ่มไปช่องถัดไป (ปกติคือ Tab)
KEY_AFTER_ROW = "enter"      # ปุ่มหลังกรอกครบ 6 ช่อง (ปกติคือ Enter เพื่อบันทึก/ขึ้นบรรทัดใหม่)
COUNTDOWN = 5                # เวลาให้สลับหน้าจอไปโปรแกรม Express
EXCEL_FILE = "data.xlsx"
# ---------------------------------------------

try:
    import openpyxl
    import pyautogui
except ImportError:
    print("กรุณาติดตั้งก่อน: pip install openpyxl pyautogui")
    sys.exit(1)

# ปิด fail-safe ถ้าไม่อยากให้เลื่อนเมาส์ไปมุมจอแล้วหยุด ให้คอมเมนต์บรรทัดนี้
pyautogui.FAILSAFE = True
pyautogui.PAUSE = 0.05

def read_excel(path):
    wb = openpyxl.load_workbook(path, data_only=True)
    ws = wb.active
    rows = []
    for i, row in enumerate(ws.iter_rows(values_only=True), 1):
        if i == 1:
            continue  # ข้ามหัวตาราง
        if all(v is None or str(v).strip() == "" for v in row):
            continue
        # เอาแค่ 6 คอลัมน์แรก
        vals = [(str(v).strip() if v is not None else "") for v in row[:6]]
        # เติมให้ครบ 6 ช่องถ้าแถวสั้น
        while len(vals) < 6:
            vals.append("")
        # แปลงวันที่ถ้าเป็น datetime
        import datetime
        for idx, v in enumerate(vals):
            if isinstance(row[idx], (datetime.datetime, datetime.date)):
                if idx == 1:  # คอลัมน์วันที่
                    vals[idx] = row[idx].strftime("%d/%m/%Y")
        rows.append(vals)
    return rows

def autofill(rows):
    print(f"พบ {len(rows)} รายการ จะเริ่มกรอกใน {COUNTDOWN} วินาที...")
    print(">>> รีบสลับไปโปรแกรม Express แล้วคลิกที่ช่อง 'เลขเอกสาร' ช่องแรก <<<")
    print("    (เลื่อนเมาส์ไปมุมซ้ายบนสุดของจอเพื่อหยุดฉุกเฉิน)")
    for i in range(COUNTDOWN, 0, -1):
        print(f"  {i}...")
        time.sleep(1)

    print("เริ่มกรอก...")
    for r_idx, row in enumerate(rows, 1):
        print(f"  [{r_idx}/{len(rows)}] {row}")
        for c_idx, val in enumerate(row):
            if val:
                pyautogui.write(val, interval=0.01)
            # ไม่ต้อง Tab หลังช่องสุดท้าย
            if c_idx < len(row) - 1:
                pyautogui.press(KEY_BETWEEN_FIELDS)
                time.sleep(DELAY_BETWEEN_FIELDS)
        # หลังครบ 1 แถว
        time.sleep(DELAY_BETWEEN_ROWS)
        pyautogui.press(KEY_AFTER_ROW)
        time.sleep(DELAY_BETWEEN_ROWS)

    print(f"เสร็จแล้ว {len(rows)} รายการ!")

if __name__ == "__main__":
    try:
        rows = read_excel(EXCEL_FILE)
    except FileNotFoundError:
        print(f"ไม่พบไฟล์ {EXCEL_FILE}")
        print("ให้สร้างไฟล์ Excel 6 คอลัมน์: เลขเอกสาร | วันที่ | รหัสลูกค้า | อ้างอิง | จำนวน | ราคา")
        print("หรือคัดลอก template.xlsx มาแก้ไขแล้วเซฟเป็น data.xlsx")
        sys.exit(1)

    if not rows:
        print("ไม่พบข้อมูลใน Excel (เช็คว่าแถว 2 เป็นต้นไปมีข้อมูล)")
        sys.exit(1)

    print(f"อ่านข้อมูลได้ {len(rows)} แถว ตัวอย่างแถวแรก: {rows[0]}")
    input("กด Enter เพื่อเริ่มนับถอยหลัง (หรือ Ctrl+C เพื่อยกเลิก)...")
    autofill(rows)
