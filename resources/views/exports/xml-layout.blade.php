<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:o="urn:schemas-microsoft-com:office:office"
          xmlns:x="urn:schemas-microsoft-com:office:excel"
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:html="http://www.w3.org/TR/REC-html40">
    <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
        <Title>{{ $title }}</Title>
        <Author>{{ $hotelName }}</Author>
        <Created>{{ now()->format('Y-m-d\TH:i:s\Z') }}</Created>
    </DocumentProperties>
    <Styles>
        <Style ss:ID="Default">
            <Alignment ss:Vertical="Bottom"/>
            <Font ss:FontName="Calibri" ss:Size="11"/>
        </Style>
        <Style ss:ID="HotelName">
            <Font ss:FontName="Calibri" ss:Size="18" ss:Bold="1" ss:Color="#1E3A8A"/>
            <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
        </Style>
        <Style ss:ID="HotelAddress">
            <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#64748B"/>
            <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
        </Style>
        <Style ss:ID="SectionTitle">
            <Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#1E3A8A"/>
            <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
        </Style>
        <Style ss:ID="GeneratedAt">
            <Font ss:FontName="Calibri" ss:Size="9" ss:Color="#94A3B8" ss:Italic="1"/>
            <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
        </Style>
        <Style ss:ID="Header">
            <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>
            <Interior ss:Color="#2563EB" ss:Pattern="Solid"/>
            <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#1D4ED8"/>
            </Borders>
        </Style>
        <Style ss:ID="Text">
            <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
        </Style>
        <Style ss:ID="Number">
            <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
            <NumberFormat ss:Format="#,##0.00"/>
        </Style>
        <Style ss:ID="Date">
            <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
            <NumberFormat ss:Format="yyyy-mm-dd"/>
        </Style>
        <Style ss:ID="Money">
            <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
            <NumberFormat ss:Format="#,##0.00_);[Red]\(#,##0.00\)"/>
        </Style>
        <Style ss:ID="Status">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
        </Style>
    </Styles>
    @yield('worksheets')
</Workbook>
