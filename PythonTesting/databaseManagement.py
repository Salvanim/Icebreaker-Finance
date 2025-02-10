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
    
    def execute(self, *command, multiline=True, fetchType="all", size = 1, multi=False):
        output = []
        if multiline:
            finalCommandString = ""
            for com in command:
                finalCommandString += com + "\n"
            self.cursor.execute(finalCommandString, multi=multi)
            output.append(self.fetch(fetchType, size))
        else:
            for com in command:
                self.cursor.execute(finalCommandString, multi=multi)
                output.append(self.fetch(fetchType, size))
        return output
    
    def viewColumnEqual(self, tableName, columnName, columnData):
        columnDataString = ""
        if type(columnData) == str:
            columnDataString = "'" + columnData + "'"
        else:
            columnDataString = columnData
        return self.execute("Select * ", f"FROM {self.user}.{tableName} ", f"WHERE {columnName} = {columnDataString}")

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
        self.commit()


    def insert(self, tableName, columns = [], values = []):
        if len(values) > len(columns):
            values = values[:len(columns)]
        while len(values) < len(columns):
            values.append("null")
        
        executeString =f"INSERT INTO {self.user}.{tableName}"
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
        output = self.execute(executeString)
        self.commit()
        return output

    def viewTable(self, tableName, *columns):
        
        output = ""
        if len(columns) == 0:
            output = self.execute("Select * ", f"FROM {self.user}.{tableName}", "WHERE 0=0;")
            self.commit()
            return output
        if len(columns) == 1:
            output = self.execute(f"Select {columns[0]} ", f"FROM {tableName}", "WHERE 0=0;")
            self.commit()
            return output
        output = self.execute(f"Select {str(tuple(columns))} ", "WHERE 0=0;", f"FROM {tableName}")
        self.commit()
        return output

    def info(self):
        self.cursor.execute(f"""SELECT * FROM 
                       INFORMATION_SCHEMA.TABLES 
                       WHERE TABLE_SCHEMA = '{self.user}'""")

        tables = []
        results = self.fetch("all")
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
        self.commit()
        return tableColumnNames

    def fetch(self, selection, size = 1):
        if type(selection) == str:
            selection.lower()
        try:
            case = switchObject.switch(
                "all", self.cursor.fetchall, 
                "many", self.cursor.fetchmany, 
                "one", self.cursor.fetchone, 
                "warn", self.cursor.fetchwarnings,
                end=self.cursor.fetchall)
            return case(selection, size)
        except:
            return ""

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
    
    def getTableNames(self):
        return list(self.info().keys())

    def clearTable(self, index, resetPrimary=True):
        self.execute("Delete FROM ", f"{self.user}.{self.getTableNames()[index]}")
        self.commit()
        if(resetPrimary):
            self.resetPrimary(self.getTableNames()[index], 0)
    
    def primaryKeys(self, tableName):
        output = self.execute("Select * ", "FROM INFORMATION_SCHEMA.COLUMNS ", f"WHERE TABLE_SCHEMA = '{self.database}' AND TABLE_NAME = '{tableName}' AND COLUMN_KEY = 'PRI'")[0][0][3]
        self.commit()
        return output
    
    def resetPrimary(self, tableName, startValue):
        primaryName = self.primaryKeys(tableName)
        self.execute(f"SET @new_id = {startValue-1};")
        self.execute(f"UPDATE {tableName}", f"SET {primaryName} = (@new_id := @new_id + 1)" , f"ORDER BY {primaryName};")
        self.execute(f"ALTER TABLE {tableName} AUTO_INCREMENT = 1;")
        self.commit()
    
    def commit(self):
        return self.db_connection.commit()
'''
db = DatabaseManager("mysql.neit.edu","5500","capstone_202520_winteriscoming","Winteriscoming","capstone_202520_winteriscoming")

info = db.info()
print(info)
db.insert(db.getTableNames()[2], info[db.getTableNames()[2]][1:], ["DylanFisher", "djfisher@email.neit.edu", "password", "admin"])
db.clearTable(2)
'''