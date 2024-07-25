import base64

# Read the TTF file
with open('NotoSansKhmer.ttf', 'rb') as font_file:
    font_data = font_file.read()

# Encode the font data to Base64
base64_encoded = base64.b64encode(font_data).decode('utf-8')

# Write the Base64 string to a text file
with open('fontBase64.txt', 'w') as text_file:
    text_file.write(base64_encoded)

# Print a success message
print("The Base64 encoded font data has been successfully written to fontBase64.txt")