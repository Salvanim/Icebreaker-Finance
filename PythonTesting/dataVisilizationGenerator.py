import matplotlib.pyplot as plt
import pandas as pd
from PIL import Image
import random

class definePlot:
    def __init__(self, data, columns):
        self.data = data
        self.columns = columns
        self.dataframe = pd.DataFrame(data, columns=columns)
        self.columnNames = list(self.dataframe.columns)
        self.fig = None
    
    def bar(self, xColumnName, yColumnname, title='', xlabel='', ylabel='', color='green', edgecolor='blue', linewidth=2):
        self.fig, ax = plt.subplots()
        ax.bar(self.dataframe[xColumnName], self.dataframe[yColumnname], color=color, edgecolor=edgecolor, linewidth=linewidth)
        ax.set_title(title)
        ax.set_xlabel(xlabel)
        ax.set_ylabel(ylabel)

    def hist(self, columnName, title='', xlabel='', ylabel='', bins=25, color='green', edgecolor='blue', linestyle='--', alpha=0.5):
        self.fig, ax = plt.subplots()
        ax.hist(self.dataframe[columnName], bins=bins, color=color, edgecolor=edgecolor, linestyle=linestyle, alpha=alpha)
        ax.set_title(title)
        ax.set_xlabel(xlabel)
        ax.set_ylabel(ylabel)
    
    def pie(self, columnName, columnLables=[], title='', explode=[], autopct='%1.2f%%', colors=[], shadow=False):
        self.fig, ax = plt.subplots()
        data = list(self.dataframe[columnName])
        while len(columnLables) < len(data):
            columnLables.append("")
        
        while len(explode) < len(data):
            explode.append(0)

        while len(colors) < len(data):
            colors.append("#"+''.join([random.choice('0123456789ABCDEF') for _ in range(6)]))

        ax.pie(data, labels=columnLables, explode=explode, colors=tuple(colors), autopct=autopct, shadow=shadow)
        ax.set_title(title)
    
    def scatter(self, xColumnName, yColumnname, title='', xlabel='', ylabel='', cName="", sName="", marker='D', alpha=0.5):
        self.fig, ax = plt.subplots()
        if cName != "" and sName != "":
            ax.scatter(self.dataframe[xColumnName], self.dataframe[yColumnname], c=self.dataframe[cName], s=self.dataframe[sName], marker=marker, alpha=alpha)
        elif cName != "":
            ax.scatter(self.dataframe[xColumnName], self.dataframe[yColumnname], c=self.dataframe[cName], marker=marker, alpha=alpha)
        elif sName != "":
            ax.scatter(self.dataframe[xColumnName], self.dataframe[yColumnname], s=self.dataframe[sName], marker=marker, alpha=alpha)
        else:
            ax.scatter(self.dataframe[xColumnName], self.dataframe[yColumnname], marker=marker, alpha=alpha)
        ax.set_title(title)
        ax.set_xlabel(xlabel)
        ax.set_ylabel(ylabel)
    
    def boxPlot(self, *columnNames, title="", xlabel="", ylabel="", vert=True, patch_artist=False, boxprops=dict(facecolor='skyblue'), medianprops=dict(color='red')):
        self.fig, ax = plt.subplots()
        data = []
        for columnName in columnNames:
            data.append(self.dataframe[columnName])
        if patch_artist:
            ax.boxplot(data, vert=vert, patch_artist=patch_artist, boxprops=boxprops, medianprops=medianprops)
        else:
            ax.boxplot(data, vert=vert, patch_artist=patch_artist)

        ax.set_xlabel(xlabel)
        ax.set_ylabel(ylabel)
        ax.set_title(title)
        
        if vert:
            xTick = ax.get_xticks().tolist()
            for i in range(len(xTick)):
                xTick[i] = columnNames[i]
            ax.set_xticklabels(xTick)
        else:
            yTick = ax.get_yticks().tolist()
            for i in range(len(yTick)):
                yTick[i] = columnNames[i]
            ax.set_yticklabels(yTick)

    def line(self, xColumnName, yColumnname, title="", xlabel="", ylabel="", color='', linewidth=3, marker='o', markersize=15, linestyle='--'):
        self.fig, ax = plt.subplots()
        if color == '':
            color = "#"+''.join([random.choice('0123456789ABCDEF') for _ in range(6)])

        ax.plot(self.dataframe[xColumnName], self.dataframe[yColumnname], color=color, linewidth=linewidth, marker=marker, markersize=markersize, linestyle=linestyle)
        ax.set_title(title)
        ax.set_ylabel(xlabel)
        ax.set_xlabel(ylabel)
    
    def heatMap(self, columnName, title="", xlabel="", ylabel="", cmap='viridis', interpolation='nearest'):
        self.fig, ax = plt.subplots()
        ax.imshow(self.dataframe[columnName].values.reshape(-1, 1), cmap=cmap, interpolation=interpolation)
        plt.colorbar(ax.imshow(self.dataframe[columnName].values.reshape(-1, 1), cmap=cmap, interpolation=interpolation), ax=ax)
        ax.set_xlabel(xlabel)
        ax.set_ylabel(ylabel)
        ax.set_title(title)
    
    def show(self):
        if self.fig:
            plt.show()
        else:
            print("No plot has been created yet.")

    def save(self, location):
        if self.fig:
            self.fig.savefig(location)
        else:
            print("No plot has been created yet.")

    def getImage(self):
        if self.fig:
            import io
            buf = io.BytesIO()
            self.fig.savefig(buf)
            buf.seek(0)
            img = Image.open(buf)
            return img
        else:
            print("No plot has been created yet.")
            return None
'''
data = [['Geek1', 28, 'Analyst', 23],
        ['Geek2', 35, 'Manager', 19],
        ['Geek3', 29, 'Developer', 30]]

# Column names
columns = ['Name', 'Age', 'Occupation', "B-Day"]

# Creating DataFrame using pd.DataFrame.from_records()
plot = definePlot(data, columns)

# Create a bar plot
plot.boxPlot("Age", "B-Day", title="title")

# Show the plot
#plot.show()
plot.getImage().save("testImageOutput.png")
'''