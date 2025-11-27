# Staff Import CSV Format - Staff Performance System

## Standard CSV Format

### Required Columns (Must be included):
1. **staff_id** - Unique identifier (e.g., IDNA102706)
2. **name** - Full name of staff
3. **phone_number** - Phone number with country code (e.g., 628123456789)
4. **position** - Job title/position
5. **department** - Department name
6. **hire_date** - Date format: YYYY-MM-DD (e.g., 2025-02-04)

### Optional Columns:
- **email** - Staff email address
- **superior** - Direct supervisor name
- **group** - Group/team designation (e.g., P-1, P-2)
- **area** - Work area/location (e.g., Jakarta, Surabaya)
- **work_location** - WFH/Onsite
- **rank** - Staff rank (e.g., S1, S2, C1, C2)
- **device** - Device assignment (PC, Laptop, etc.)
- **team_leader_id** - ID of team leader if applicable
- **warning_letter** - Warning letter status (leave empty if none)
- **ojk_case** - OJK case count (default: 0)
- **notes** - Additional notes

## Important Rules:

1. **Phone numbers**: Must start with country code (62 for Indonesia)
   - Example: 628123456789 (not 08123456789)

2. **Date format**: Always use YYYY-MM-DD
   - Correct: 2025-02-04
   - Wrong: 2/4/2025 or 04-02-2025

3. **No line breaks** in any cell values

4. **Empty values**: Use empty string, not "-" or "N/A"

5. **CSV encoding**: UTF-8 (to support special characters)

6. **Delimiter**: Comma (,)

## File Location:
Save your import file as: `staff_import_data.csv`

## Example with Multiple Staff:
See `staff_import_example.csv` for full example with 5 staff members.
