// Contoh penggunaan ExcelJS sebagai pengganti xlsx
import * as ExcelJS from 'exceljs';

// 1. Membaca file Excel
export async function readExcelFile(filePath: string) {
  const workbook = new ExcelJS.Workbook();
  await workbook.xlsx.readFile(filePath);

  const worksheet = workbook.getWorksheet(1); // Sheet pertama
  const data = [];

  worksheet.eachRow((row, rowNumber) => {
    if (rowNumber > 1) {
      // Skip header
      data.push({
        col1: row.getCell(1).value,
        col2: row.getCell(2).value,
        col3: row.getCell(3).value,
      });
    }
  });

  return data;
}

// 2. Membuat file Excel
export async function createExcelFile(data: any[], filePath: string) {
  const workbook = new ExcelJS.Workbook();
  const worksheet = workbook.addWorksheet('Data');

  // Header
  worksheet.columns = [
    { header: 'Nama', key: 'nama', width: 20 },
    { header: 'Email', key: 'email', width: 30 },
    { header: 'Status', key: 'status', width: 15 },
  ];

  // Data
  worksheet.addRows(data);

  // Styling
  worksheet.getRow(1).font = { bold: true };
  worksheet.getRow(1).fill = {
    type: 'pattern',
    pattern: 'solid',
    fgColor: { argb: 'FFE0E0E0' },
  };

  await workbook.xlsx.writeFile(filePath);
}

// 3. Membaca dari buffer (untuk upload file)
export async function readExcelFromBuffer(buffer: ArrayBuffer) {
  const workbook = new ExcelJS.Workbook();
  await workbook.xlsx.load(buffer);

  const worksheet = workbook.getWorksheet(1);
  const data = [];

  worksheet.eachRow((row, rowNumber) => {
    if (rowNumber > 1) {
      // Skip header
      const rowData = {};
      row.eachCell((cell, colNumber) => {
        rowData[`col${colNumber}`] = cell.value;
      });
      data.push(rowData);
    }
  });

  return data;
}

// 4. Export ke response HTTP
export async function exportToExcelResponse(data: any[], response: any) {
  const workbook = new ExcelJS.Workbook();
  const worksheet = workbook.addWorksheet('Data Export');

  worksheet.columns = [
    { header: 'ID', key: 'id', width: 10 },
    { header: 'Nama', key: 'nama', width: 20 },
    { header: 'Email', key: 'email', width: 30 },
    { header: 'Tanggal', key: 'tanggal', width: 15 },
  ];

  worksheet.addRows(data);

  response.setHeader(
    'Content-Type',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  );
  response.setHeader('Content-Disposition', 'attachment; filename=data-export.xlsx');

  await workbook.xlsx.write(response);
}
