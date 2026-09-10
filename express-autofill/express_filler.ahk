; Express AutoFill (AutoHotkey v1.1)
; กรอก 6 ช่อง: เลขเอกสาร | วันที่ | รหัสลูกค้า | อ้างอิง | จำนวน | ราคา
; วิธีใช้:
; 1. เตรียมไฟล์ data.csv ไว้โฟลเดอร์เดียวกับไฟล์นี้ (คอลัมน์: เลขเอกสาร,วันที่,รหัสลูกค้า,อ้างอิง,จำนวน,ราคา)
; 2. เปิดโปรแกรม Express วางเคอร์เซอร์ที่ช่อง "เลขเอกสาร" ช่องแรก
; 3. กด F8 เพื่อเริ่มกรอกทีละแถว, กด Esc เพื่อหยุดฉุกเฉิน

#NoEnv
SendMode Input
SetWorkingDir %A_ScriptDir%
SetBatchLines, -1

dataFile := A_ScriptDir . "\data.csv"
delayField := 150   ; มิลลิวินาทีระหว่างช่อง
delayRow := 500     ; มิลลิวินาทีระหว่างเอกสาร

F8::
    if (!FileExist(dataFile)) {
        MsgBox, ไม่พบไฟล์ %dataFile%`nให้สร้างไฟล์ CSV 6 คอลัมน์: เลขเอกสาร,วันที่,รหัสลูกค้า,อ้างอิง,จำนวน,ราคา
        return
    }
    MsgBox, 4, Express AutoFill, จะเริ่มกรอกใน 5 วินาที`n`nกรุณาคลิกที่ช่อง "เลขเอกสาร" ช่องแรกในโปรแกรม Express แล้วรอ`n`nกด Yes เพื่อเริ่ม / No เพื่อยกเลิก
    IfMsgBox, No
        return
    Sleep, 5000
    rowCount := 0
    Loop, Read, %dataFile%
    {
        line := A_LoopReadLine
        if (A_Index = 1)  ; ข้ามหัวตารางแถวแรก
            continue
        if (Trim(line) = "")
            continue
        fields := StrSplit(line, ",")
        ; เติมให้ครบ 6 ช่องถ้าแถวสั้น
        Loop, 6 {
            val := fields[A_Index] ? Trim(fields[A_Index]) : ""
            if (val != "")
                SendRaw, %val%
            if (A_Index < 6) {
                Send, {Tab}
                Sleep, %delayField%
            }
        }
        rowCount++
        Sleep, %delayRow%
        Send, {Enter}
        Sleep, %delayRow%
    }
    MsgBox, กรอกเสร็จแล้ว %rowCount% รายการ!
    return

Esc::ExitApp
