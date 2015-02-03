BASE=eurecap
PLUGINTYPE=vmextended
VERSION=0.1

PLUGINFILES=$(BASE).php $(BASE).script.php $(BASE).xml index.html 
# $(BASE)/
MVCFILES=controllers/ models/ views/

SYSTRANSLATIONS=$(call wildcard,language/*/*.plg_$(PLUGINTYPE)_$(BASE).*sys.ini)
NONSYSTRANSLATIONS=${SYSTRANSLATIONS:%.sys.ini=%.ini}
TRANSLATIONS=$(SYSTRANSLATIONS) $(NONSYSTRANSLATIONS) $(call wildcard,language/*/index.html) language/index.html
FIELDS=$(call wildcard,fields/*.php) 
ZIPFILE=plg_$(PLUGINTYPE)_$(BASE)_v$(VERSION).zip


all: zip

$(NONSYSTRANSLATIONS): %.ini: %.sys.ini
	cp $< $@

zip: $(PLUGINFILES) $(TRANSLATIONS) $(SYSTRANSLATIONS) $(NONSYSTRANSLATIONS)
	@echo "Packing all files into distribution file $(ZIPFILE):"
	@zip -r $(ZIPFILE) $(PLUGINFILES) $(MVCFILES) $(TRANSLATIONS) $(FIELDS)

clean:
	rm -f $(ZIPFILE)
