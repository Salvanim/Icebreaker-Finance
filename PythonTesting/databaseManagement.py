import mysql.connector
import switchObject

class DatabaseManager():
    def __init__(self, host, port, user, password, database):
        self.host = host
        self.port = port
        self.user = user
        self.password = password
        self.database = database

        self.db_connection = mysql.connector.connect(
            host=host,
            port=port,
            user=user,
            passwd=password,
            database=database
        )
        self.cursor = self.db_connection.cursor()
    
    def execute(self, *command, multiline=True, fetchType="all", size = 1):
        output = []
        if multiline:
            finalCommandString = ""
            for com in command:
                finalCommandString += com + "\n"
            self.cursor.execute(finalCommandString)
            output.append(self.fetch(fetchType, size))
        else:
            for com in command:
                self.cursor.execute(finalCommandString)
                output.append(self.fetch(fetchType, size))
        return output

    def update(self, tableName, columns=[], change=[], values=[], conditions=[]):
        if len(values) > len(columns):
            values = values[:len(columns)]
        while len(values) < len(columns):
            values.append("null")
        
        while len(change) < len(columns):
            change.append('=')
        
        executeString = f"Update {tableName}"
        updateStrings = []
        for i in range(len(columns)):
            updateStrings.append(str(columns[i]) + str(change[i]) + str(values[i]))
        
        for string in updateStrings:
            executeString += "\n" + string
        
        conditionalString = "Where "
        for con in conditions:
            conditionalString += con + " OR "
        conditionalString += " 0=0"

        executeString += "\n" + conditionalString
        self.execute(executeString)


    def insert(self, tableName, columns = [], values = []):
        if len(values) > len(columns):
            values = values[:len(columns)]
        while len(values) < len(columns):
            values.append("null")
        
        executeString =f"INSERT INTO {tableName}"
        insertValues = "("
        valueValues = "Values ("
        for i in range(len(columns)):
            insertValues += columns[i]
            if type(values[i]) == str:
                valueValues += "'" + values[i] + "'"
            else:
                valueValues += str(values[i])
            if i != len(columns)-1:
                insertValues += ","
                valueValues += ","
        insertValues += ")"
        valueValues += ")"
        executeString += insertValues + "\n" + valueValues
        return self.execute(executeString)

    def viewTable(self, tableName, *columns):
        return self.execute(f"Select {str(tuple(columns))} FROM {tableName}")

    def info(self):
        self.cursor.execute(f"""SELECT * FROM 
                       INFORMATION_SCHEMA.TABLES 
                       WHERE TABLE_SCHEMA = '{self.user}'""")

        tables = []
        results = self.cursor.fetchall()
        for row in results:
            tables.append(row[2])

        tableColumnNames = {}
        for table in tables:
            tableColumnNames[table] = []
            cursor2 = self.db_connection.cursor()
            cursor2.execute(f"""SELECT *
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME = N'{table}'""")
            results2 = cursor2.fetchall()
            for rows in results2:
                tableColumnNames[table].append(rows[3])
        return tableColumnNames

    def fetch(self, selection, size = 1):
        case = switchObject.switch(
            "all", self.cursor.fetchall, 
            "many", self.cursor.fetchmany, 
            "one", self.cursor.fetchone, 
            "warn", self.cursor.fetchwarnings, 
            "sets", self.cursor.fetchsets,
            end= self.cursor.fetchall)
        
        return case(selection, size)

    def close(self):
        self.cursor.close()
        self.db_connection.close()
    
    def open(self):
        self.db_connection = mysql.connector.connect(
            host=self.host,
            port=self.port,
            user=self.user,
            passwd=self.password,
            database=self.database
        )
        self.cursor = self.db_connection.cursor()

db = DatabaseManager("mysql.neit.edu","5500","capstone_202520_winteriscoming","Winteriscoming","capstone_202520_winteriscoming")

info = db.info()
print(info)
db.insert(list(info.keys())[2], info[list(info.keys())[2]][1:], ["DylanFisher", "djfisher@email.neit.edu", "password", "admin"])
db.execute("Delete FROM", list(info.keys())[2])