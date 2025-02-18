import matplotlib.pyplot as plt
import pandas as pd
from PIL import Image
import random

class DefinePlot:
    def __init__(self, data, columns):
        self.data = data
        self.columns = columns
        self.dataframe = pd.DataFrame(data, columns=columns)
        self.columnNames = list(self.dataframe.columns)
        self.fig = None
        self.methodVariables = {
            "bar": ["xColumnName", "yColumnName", "title", "xlabel", "ylabel", "color", "edgecolor", "linewidth"],
            "hist": ["columnName", "title", "xlabel", "ylabel", "bins", "color", "edgecolor", "linestyle", "alpha"],
            "pie": ["columnName", "columnLabels", "title", "explode", "autopct", "colors", "shadow"],
            "scatter": ["xColumnName", "yColumnName", "title", "xlabel", "ylabel", "cName", "sName", "marker", "alpha"],
            "box": ["*columnNames", "title", "xlabel", "ylabel", "vert", "patch_artist", "boxprops", "medianprops"],
            "line": ["xColumnName", "yColumnName", "title", "xlabel", "ylabel", "color", "linewidth", "marker", "markersize", "linestyle"],
            "heat": ["columnName", "title", "xlabel", "ylabel", "cmap", "interpolation"]
        }
    
    def bar(self, xColumnName, yColumnName, title='', xlabel='', ylabel='', color='green', edgecolor='blue', linewidth=2):
        self.fig, ax = plt.subplots()
        ax.bar(self.dataframe[xColumnName], self.dataframe[yColumnName], color=color, edgecolor=edgecolor, linewidth=linewidth)
        ax.set_title(title)
        ax.set_xlabel(xlabel)
        ax.set_ylabel(ylabel)

    def hist(self, columnName, title='', xlabel='', ylabel='', bins=25, color='green', edgecolor='blue', linestyle='--', alpha=0.5):
        self.fig, ax = plt.subplots()
        ax.hist(self.dataframe[columnName], bins=bins, color=color, edgecolor=edgecolor, linestyle=linestyle, alpha=alpha)
        ax.set_title(title)
        ax.set_xlabel(xlabel)
        ax.set_ylabel(ylabel)
    
    def pie(self, columnName, columnLabels=[], title='', explode=[], autopct='%1.2f%%', colors=[], shadow=False):
        self.fig, ax = plt.subplots()
        data = list(self.dataframe[columnName])
        columnLabels.extend(["" for _ in range(len(data) - len(columnLabels))])
        explode.extend([0 for _ in range(len(data) - len(explode))])
        colors.extend(["#"+''.join(random.choices('0123456789ABCDEF', k=6)) for _ in range(len(data) - len(colors))])
        
        ax.pie(data, labels=columnLabels, explode=explode, colors=tuple(colors), autopct=autopct, shadow=shadow)
        ax.set_title(title)
    
    def scatter(self, xColumnName, yColumnName, title='', xlabel='', ylabel='', cName="", sName="", marker='D', alpha=0.5):
        self.fig, ax = plt.subplots()
        if cName and sName:
            ax.scatter(self.dataframe[xColumnName], self.dataframe[yColumnName], c=self.dataframe[cName], s=self.dataframe[sName], marker=marker, alpha=alpha)
        elif cName:
            ax.scatter(self.dataframe[xColumnName], self.dataframe[yColumnName], c=self.dataframe[cName], marker=marker, alpha=alpha)
        elif sName:
            ax.scatter(self.dataframe[xColumnName], self.dataframe[yColumnName], s=self.dataframe[sName], marker=marker, alpha=alpha)
        else:
            ax.scatter(self.dataframe[xColumnName], self.dataframe[yColumnName], marker=marker, alpha=alpha)
        ax.set_title(title)
        ax.set_xlabel(xlabel)
        ax.set_ylabel(ylabel)
    
    def boxPlot(self, *columnNames, title="", xlabel="", ylabel="", vert=True, patch_artist=False, boxprops=dict(facecolor='skyblue'), medianprops=dict(color='red')):
        self.fig, ax = plt.subplots()
        data = [self.dataframe[column] for column in columnNames]
        ax.boxplot(data, vert=vert, patch_artist=patch_artist, boxprops=boxprops, medianprops=medianprops)
        ax.set_xlabel(xlabel)
        ax.set_ylabel(ylabel)
        ax.set_title(title)
        ax.set_xticklabels(columnNames if vert else ax.get_yticks())
    
    def line(self, xColumnName, yColumnName, title="", xlabel="", ylabel="", color='', linewidth=3, marker='o', markersize=15, linestyle='--'):
        self.fig, ax = plt.subplots()
        if not color:
            color = "#"+''.join(random.choices('0123456789ABCDEF', k=6))
        ax.plot(self.dataframe[xColumnName], self.dataframe[yColumnName], color=color, linewidth=linewidth, marker=marker, markersize=markersize, linestyle=linestyle)
        ax.set_title(title)
        ax.set_xlabel(xlabel)
        ax.set_ylabel(ylabel)
    
    def heatMap(self, columnName, title="", xlabel="", ylabel="", cmap='viridis', interpolation='nearest'):
        self.fig, ax = plt.subplots()
        img = ax.imshow(self.dataframe[columnName].values.reshape(-1, 1), cmap=cmap, interpolation=interpolation)
        plt.colorbar(img, ax=ax)
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
            return Image.open(buf)
        else:
            print("No plot has been created yet.")
            return None
